<?php

namespace App\Controllers;

use App\Models\ListModel;
use CodeIgniter\Controller;

class ListController extends Controller
{
    protected $listModel;
    protected $helpers = ['url', 'obfuscator'];

    public function __construct()
    {
        $this->listModel = new ListModel();
    }

    public function store()
    {
        $boardId = decode_id($this->request->getPost('board_id'));
        $title = $this->request->getPost('title');

        if (empty($boardId) || empty($title)) {
            session()->setFlashdata('error', 'ID Workspace dan judul wajib diisi.');
            return redirect()->back();
        }

        // Get max position to append list to end
        $maxPosition = $this->listModel->where('board_id', $boardId)->selectMax('position')->first();
        $position = ($maxPosition['position'] ?? 0) + 1;

        $this->listModel->save([
            'board_id' => $boardId,
            'title'    => $title,
            'position' => $position,
        ]);

        session()->setFlashdata('success', 'Daftar berhasil ditambahkan.');
        return redirect()->back();
    }

    public function update($id)
    {
        $decodedId = decode_id($id);
        $list = $this->listModel->find($decodedId);
        if (!$list) {
            return $this->response->setJSON(['success' => false, 'message' => 'Daftar tidak ditemukan.']);
        }

        $title = $this->request->getPost('title');
        if (empty($title)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Judul wajib diisi.']);
        }

        $this->listModel->update($decodedId, [
            'title' => $title,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Daftar berhasil diperbarui.']);
    }

    public function delete($id)
    {
        $decodedId = decode_id($id);
        $list = $this->listModel->find($decodedId);
        if (!$list) {
            session()->setFlashdata('error', 'Daftar tidak ditemukan.');
            return redirect()->back();
        }

        $this->listModel->delete($decodedId);
        session()->setFlashdata('success', 'Daftar berhasil dihapus.');
        return redirect()->back();
    }
}
