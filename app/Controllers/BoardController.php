<?php

namespace App\Controllers;

use App\Models\BoardModel;
use App\Models\ListModel;
use App\Models\CardModel;
use CodeIgniter\Controller;

class BoardController extends Controller
{
    protected $boardModel;
    protected $listModel;
    protected $cardModel;
    protected $helpers = ['url', 'obfuscator'];

    public function __construct()
    {
        $this->boardModel = new BoardModel();
        $this->listModel = new ListModel();
        $this->cardModel = new CardModel();
    }

    public function index()
    {
        // Bersihkan status unlock di session saat kembali ke dashboard (selalu kunci kembali)
        session()->remove('unlocked_boards');

        $boards = $this->boardModel->orderBy('created_at', 'DESC')->findAll();
        foreach ($boards as &$board) {
            $board['id'] = encode_id($board['id']);
        }

        return view('boards/index', [
            'title' => 'Dashboard | Eroom',
            'boards' => $boards,
        ]);
    }

    public function show($id)
    {
        // Cegah caching browser agar Turbo cache / riwayat navigasi tidak menampilkan isi board yang terkunci
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
        $this->response->setHeader('Pragma', 'no-cache');

        $decodedId = decode_id($id);
        $board = $this->boardModel->find($decodedId);
        if (!$board) {
            session()->setFlashdata('error', 'Workspace tidak ditemukan.');
            return redirect()->to(base_url('/'));
        }

        // Cek proteksi kata sandi
        if (!empty($board['password'])) {
            $unlocked = session()->get('unlocked_boards') ?? [];
            if (!in_array($decodedId, $unlocked)) {
                $board['id'] = encode_id($board['id']);
                return view('boards/unlock', [
                    'title' => 'Workspace Terkunci | Eroom',
                    'board' => $board,
                ]);
            }
        }

        $board['id'] = encode_id($board['id']);

        // Ambil daftar (lists) untuk board ini sesuai urutan posisi
        $lists = $this->listModel->where('board_id', $decodedId)->orderBy('position', 'ASC')->findAll();

        $attachmentModel = new \App\Models\AttachmentModel();
        foreach ($lists as &$list) {
            $list['id'] = encode_id($list['id']);
            $list['board_id'] = encode_id($list['board_id']);
            $list['cards'] = $this->cardModel->where('list_id', decode_id($list['id']))->orderBy('position', 'ASC')->findAll();
            foreach ($list['cards'] as &$card) {
                $card['id'] = encode_id($card['id']);
                $card['list_id'] = encode_id($card['list_id']);
                $card['attachments'] = $attachmentModel->where('card_id', decode_id($card['id']))->findAll();
                foreach ($card['attachments'] as &$att) {
                    $att['id'] = encode_id($att['id']);
                    $att['card_id'] = encode_id($att['card_id']);
                }
            }
        }

        // Ambil semua board untuk navigasi
        $allBoards = $this->boardModel->orderBy('title', 'ASC')->findAll();
        foreach ($allBoards as &$ab) {
            $ab['id'] = encode_id($ab['id']);
        }

        return view('boards/show', [
            'title' => $board['title'] . ' | Eroom',
            'meta_description' => 'Kelola proyek dan tugas secara kolaboratif pada workspace "' . esc($board['title']) . '" di Eroom dengan tampilan claymorphism interaktif.',
            'board' => $board,
            'lists' => $lists,
            'allBoards' => $allBoards,
        ]);
    }

    public function unlock($id)
    {
        $decodedId = decode_id($id);
        $board = $this->boardModel->find($decodedId);
        if (!$board) {
            session()->setFlashdata('error', 'Workspace tidak ditemukan.');
            return redirect()->to(base_url('/'));
        }

        // Brute-force protection / Rate limiting per IP dan Board
        $ip = $this->request->getIPAddress();
        $rateLimitKey = 'unlock_attempts_' . md5($ip . '_' . $decodedId);
        $attempts = session()->get($rateLimitKey) ?? ['count' => 0, 'locked_until' => 0];

        if ($attempts['locked_until'] > time()) {
            $remaining = $attempts['locked_until'] - time();
            session()->setFlashdata('error', "Terlalu banyak percobaan gagal. Silakan coba lagi dalam {$remaining} detik.");
            return redirect()->to(base_url('/boards/' . $id));
        }

        $password = (string) $this->request->getPost('password');
        if (password_verify($password, $board['password'] ?? '')) {
            // Berhasil login / buka kunci: reset batas percobaan
            session()->remove($rateLimitKey);

            $unlocked = session()->get('unlocked_boards') ?? [];
            if (!in_array($decodedId, $unlocked)) {
                $unlocked[] = $decodedId;
                session()->set('unlocked_boards', $unlocked);
            }
            session()->setFlashdata('success', 'Workspace berhasil dibuka.');
            return redirect()->to(base_url('/boards/' . $id));
        }

        // Gagal buka kunci: tambahkan counter percobaan
        $attempts['count']++;
        if ($attempts['count'] >= 5) {
            $attempts['locked_until'] = time() + 300; // Blokir selama 5 menit (300 detik)
            $attempts['count'] = 0;
            session()->set($rateLimitKey, $attempts);
            session()->setFlashdata('error', 'Terlalu banyak percobaan salah. Akses ditangguhkan selama 5 menit demi keamanan.');
            return redirect()->to(base_url('/boards/' . $id));
        }

        session()->set($rateLimitKey, $attempts);
        $sisaPercobaan = 5 - $attempts['count'];
        session()->setFlashdata('error', "Kata sandi yang Anda masukkan salah. Sisa percobaan: {$sisaPercobaan}x.");
        return redirect()->to(base_url('/boards/' . $id));
    }

    public function lock($id)
    {
        $decodedId = decode_id($id);
        $unlocked = session()->get('unlocked_boards') ?? [];
        if (($key = array_search($decodedId, $unlocked)) !== false) {
            unset($unlocked[$key]);
            session()->set('unlocked_boards', array_values($unlocked));
        }

        session()->setFlashdata('success', 'Anda telah berhasil keluar dari workspace.');
        return redirect()->to(base_url('/'));
    }

    public function store()
    {
        $isProtected = $this->request->getPost('is_protected') === '1';

        $validationRules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'color' => 'required',
        ];

        $validationMessages = [
            'title' => [
                'required'   => 'Nama Workspace harus diisi.',
                'min_length' => 'Nama Workspace minimal 3 karakter.',
                'max_length' => 'Nama Workspace maksimal 255 karakter.',
            ],
            'color' => [
                'required' => 'Warna aksen harus dipilih.',
            ],
        ];

        if ($isProtected) {
            $validationRules['password'] = 'required|min_length[4]|max_length[255]';
            $validationMessages['password'] = [
                'required'   => 'Kata sandi harus diisi jika workspace diproteksi.',
                'min_length' => 'Kata sandi workspace minimal 4 karakter.',
                'max_length' => 'Kata sandi workspace maksimal 255 karakter.',
            ];
        }

        if (!$this->validate($validationRules, $validationMessages)) {
            session()->setFlashdata('error', 'Gagal membuat workspace: ' . implode(' ', $this->validator->getErrors()));
            return redirect()->to(base_url('/'));
        }

        $passwordHash = null;
        if ($isProtected && !empty($this->request->getPost('password'))) {
            $passwordHash = password_hash((string)$this->request->getPost('password'), PASSWORD_BCRYPT);
        }

        $boardData = [
            'title'    => esc(trim($this->request->getPost('title'))),
            'color'    => $this->request->getPost('color'),
            'password' => $passwordHash,
        ];

        $insertedId = $this->boardModel->insert($boardData);

        // Jika terproteksi, tambahkan ke session pembuat agar langsung terbuka
        if ($isProtected) {
            $unlocked = session()->get('unlocked_boards') ?? [];
            $unlocked[] = $insertedId;
            session()->set('unlocked_boards', $unlocked);
        }

        session()->setFlashdata('success', 'Workspace berhasil dibuat.');
        return redirect()->to(base_url('/boards/' . encode_id($insertedId)));
    }

    public function updateSettings($id)
    {
        $decodedId = decode_id($id);
        $board = $this->boardModel->find($decodedId);
        if (!$board) {
            session()->setFlashdata('error', 'Workspace tidak ditemukan.');
            return redirect()->to(base_url('/'));
        }

        // Verifikasi kata sandi saat ini jika workspace sebelumnya terkunci
        if (!empty($board['password'])) {
            $currentPassword = (string)$this->request->getPost('current_password');
            if (empty($currentPassword) || !password_verify($currentPassword, $board['password'])) {
                session()->setFlashdata('error', 'Gagal menyimpan: Kata sandi saat ini tidak valid.');
                return redirect()->back();
            }
        }

        $title = trim((string)$this->request->getPost('title'));
        $color = (string)$this->request->getPost('color');
        $securityType = (string)$this->request->getPost('security_type'); // 'public' or 'protected'
        $newPassword = (string)$this->request->getPost('new_password');

        if (empty($title) || strlen($title) < 3) {
            session()->setFlashdata('error', 'Nama workspace minimal 3 karakter.');
            return redirect()->back();
        }

        $updateData = [
            'title' => esc($title),
            'color' => $color ?: $board['color'],
        ];

        if ($securityType === 'public') {
            // Ubah menjadi workspace publik tanpa kunci
            $updateData['password'] = null;
        } elseif ($securityType === 'protected') {
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 4) {
                    session()->setFlashdata('error', 'Kata sandi baru minimal 4 karakter.');
                    return redirect()->back();
                }
                $updateData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
            }
            // Jika newPassword kosong dan sebelumnya sudah ada password, biarkan password lama
        }

        $this->boardModel->update($decodedId, $updateData);
        session()->setFlashdata('success', 'Pengaturan workspace berhasil diperbarui.');
        return redirect()->to(base_url('/boards/' . $id));
    }

    public function delete($id)
    {
        $decodedId = decode_id($id);
        $board = $this->boardModel->find($decodedId);
        if (!$board) {
            session()->setFlashdata('error', 'Workspace tidak ditemukan.');
            return redirect()->to(base_url('/'));
        }

        // Cek kata sandi workspace jika terproteksi
        if (!empty($board['password'])) {
            $password = (string)$this->request->getPost('password');
            if (empty($password) || !password_verify($password, $board['password'])) {
                session()->setFlashdata('error', 'Gagal menghapus: Kata sandi workspace salah.');
                return redirect()->back();
            }
        }

        // Hapus file fisik lampiran
        $attachmentModel = new \App\Models\AttachmentModel();
        $lists = $this->listModel->where('board_id', $decodedId)->findAll();
        $listIds = array_column($lists, 'id');

        if (!empty($listIds)) {
            $cards = $this->cardModel->whereIn('list_id', $listIds)->findAll();
            $cardIds = array_column($cards, 'id');

            if (!empty($cardIds)) {
                $attachments = $attachmentModel->whereIn('card_id', $cardIds)->findAll();
                foreach ($attachments as $att) {
                    $filePath = FCPATH . 'uploads/' . $att['file_name'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }

        $this->boardModel->delete($decodedId);
        session()->setFlashdata('success', 'Workspace berhasil dihapus.');
        return redirect()->to(base_url('/'));
    }

    public function sitemap()
    {
        $boards = $this->boardModel->where('password IS NULL', null, false)->orderBy('updated_at', 'DESC')->findAll();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Homepage
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . base_url('/') . "</loc>\n";
        $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $xml .= "    <changefreq>daily</changefreq>\n";
        $xml .= "    <priority>1.0</priority>\n";
        $xml .= "  </url>\n";

        // Public boards
        foreach ($boards as $board) {
            $date = !empty($board['updated_at']) ? date('Y-m-d', strtotime($board['updated_at'])) : date('Y-m-d');
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . base_url('/boards/' . encode_id($board['id'])) . "</loc>\n";
            $xml .= "    <lastmod>{$date}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $this->response
            ->setHeader('Content-Type', 'application/xml; charset=utf-8')
            ->setBody($xml);
    }

    public function getMessages($id)
    {
        $decodedId = decode_id($id);
        $board = $this->boardModel->find($decodedId);
        if (!$board) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Workspace tidak ditemukan']);
        }

        // Cek jika workspace terproteksi maka harus sudah di-unlock
        if (!empty($board['password'])) {
            $unlocked = session()->get('unlocked_boards') ?? [];
            if (!in_array($decodedId, $unlocked)) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
            }
        }

        $messageModel = new \App\Models\WorkspaceMessageModel();
        $messages = $messageModel->where('board_id', $decodedId)->orderBy('created_at', 'ASC')->findAll(100);

        foreach ($messages as &$msg) {
            $msg['formatted_time'] = date('H:i', strtotime($msg['created_at']));
            $msg['sender_name'] = esc($msg['sender_name']);
            $msg['message'] = esc($msg['message']);
        }

        return $this->response->setJSON([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    public function sendMessage($id)
    {
        $decodedId = decode_id($id);
        $board = $this->boardModel->find($decodedId);
        if (!$board) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Workspace tidak ditemukan']);
        }

        if (!empty($board['password'])) {
            $unlocked = session()->get('unlocked_boards') ?? [];
            if (!in_array($decodedId, $unlocked)) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
            }
        }

        $senderName = trim((string)$this->request->getPost('sender_name'));
        $messageText = trim((string)$this->request->getPost('message'));

        if (empty($senderName) || empty($messageText)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Nama pengirim dan pesan tidak boleh kosong.']);
        }

        // Simpan nama di sesi agar user tidak perlu mengetik nama berulang kali
        session()->set('chat_username', esc(mb_substr($senderName, 0, 50)));

        $messageModel = new \App\Models\WorkspaceMessageModel();
        $messageId = $messageModel->insert([
            'board_id'    => $decodedId,
            'sender_name' => mb_substr($senderName, 0, 50),
            'message'     => mb_substr($messageText, 0, 2000),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pesan terkirim',
            'data'    => [
                'id'             => $messageId,
                'sender_name'    => esc(mb_substr($senderName, 0, 50)),
                'message'        => esc(mb_substr($messageText, 0, 2000)),
                'formatted_time' => date('H:i'),
            ]
        ]);
    }
}
