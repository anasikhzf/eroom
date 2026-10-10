<?php

namespace App\Controllers;

use App\Models\CardModel;
use CodeIgniter\Controller;

class CardController extends Controller
{
    protected $cardModel;
    protected $helpers = ['url', 'obfuscator'];

    public function __construct()
    {
        $this->cardModel = new CardModel();
    }

    public function store()
    {
        $listId = decode_id($this->request->getPost('list_id'));
        $title = $this->request->getPost('title');

        if (empty($listId) || empty($title)) {
            session()->setFlashdata('error', 'ID Daftar dan judul kartu wajib diisi.');
            return redirect()->back();
        }

        // Get max position in the list
        $maxPosition = $this->cardModel->where('list_id', $listId)->selectMax('position')->first();
        $position = ($maxPosition['position'] ?? 0) + 1;

        $cardId = $this->cardModel->insert([
            'list_id'     => $listId,
            'title'       => $title,
            'description' => $this->request->getPost('description') ?: null,
            'due_date'    => $this->request->getPost('due_date') ?: null,
            'label_color' => $this->request->getPost('label_color') ?: null,
            'position'    => $position,
        ]);

        if ($cardId) {
            // Upload attachment if any during creation
            $file = $this->request->getFile('media');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                if (!is_dir(FCPATH . 'uploads')) {
                    mkdir(FCPATH . 'uploads', 0777, true);
                }

                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads', $newName);

                $attachmentModel = new \App\Models\AttachmentModel();
                $attachmentModel->save([
                    'card_id'       => $cardId,
                    'file_name'     => $newName,
                    'original_name' => $file->getClientName(),
                    'file_type'     => $file->getClientMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }

            session()->setFlashdata('open_card_id', encode_id($cardId));
        }

        session()->setFlashdata('success', 'Kartu berhasil dibuat.');
        return redirect()->back();
    }

    public function update($id)
    {
        $decodedId = decode_id($id);
        $card = $this->cardModel->find($decodedId);
        if (!$card) {
            session()->setFlashdata('error', 'Kartu tidak ditemukan.');
            return redirect()->back();
        }

        $title = $this->request->getPost('title');
        if (empty($title)) {
            session()->setFlashdata('error', 'Judul kartu wajib diisi.');
            return redirect()->back();
        }

        $this->cardModel->update($decodedId, [
            'title'       => $title,
            'description' => $this->request->getPost('description') ?: null,
            'due_date'    => $this->request->getPost('due_date') ?: null,
            'label_color' => $this->request->getPost('label_color') ?: null,
        ]);

        // Upload attachment if any
        $file = $this->request->getFile('media');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(FCPATH . 'uploads')) {
                mkdir(FCPATH . 'uploads', 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads', $newName);

            $attachmentModel = new \App\Models\AttachmentModel();
            $attachmentModel->save([
                'card_id'       => $decodedId,
                'file_name'     => $newName,
                'original_name' => $file->getClientName(),
                'file_type'     => $file->getClientMimeType(),
                'file_size'     => $file->getSize(),
            ]);
        }

        session()->setFlashdata('open_card_id', encode_id($decodedId));
        session()->setFlashdata('success', 'Kartu berhasil diperbarui.');
        return redirect()->back();
    }

    public function delete($id)
    {
        $decodedId = decode_id($id);
        $card = $this->cardModel->find($decodedId);
        if (!$card) {
            session()->setFlashdata('error', 'Kartu tidak ditemukan.');
            return redirect()->back();
        }

        $this->cardModel->delete($decodedId);
        session()->setFlashdata('success', 'Kartu berhasil dihapus.');
        return redirect()->back();
    }

    public function move($id)
    {
        $decodedId = decode_id($id);
        $card = $this->cardModel->find($decodedId);
        if (!$card) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kartu tidak ditemukan.']);
        }

        $targetListId = decode_id($this->request->getPost('target_list_id'));
        $orderedCardIds = $this->request->getPost('card_ids'); // Array of IDs in the target list after drop

        if (empty($targetListId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID Daftar target wajib diisi.']);
        }

        // Update list ID of the dragged card
        $this->cardModel->update($decodedId, [
            'list_id' => $targetListId
        ]);

        // Re-sequence the positions of the target list's cards
        if (is_array($orderedCardIds)) {
            foreach ($orderedCardIds as $index => $cId) {
                if (!empty($cId)) {
                    $this->cardModel->update(decode_id($cId), [
                        'position' => $index
                    ]);
                }
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Kartu berhasil dipindahkan.']);
    }

    public function uploadAttachment($id)
    {
        $decodedId = decode_id($id);
        $card = $this->cardModel->find($decodedId);
        if (!$card) {
            session()->setFlashdata('error', 'Kartu tidak ditemukan.');
            return redirect()->back();
        }

        $file = $this->request->getFile('media');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(FCPATH . 'uploads')) {
                mkdir(FCPATH . 'uploads', 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads', $newName);

            $attachmentModel = new \App\Models\AttachmentModel();
            $attachmentModel->save([
                'card_id'       => $decodedId,
                'file_name'     => $newName,
                'original_name' => $file->getClientName(),
                'file_type'     => $file->getClientMimeType(),
                'file_size'     => $file->getSize(),
            ]);

            session()->setFlashdata('success', 'Lampiran berhasil diunggah.');
        } else {
            session()->setFlashdata('error', 'Gagal mengunggah lampiran.');
        }

        return redirect()->back();
    }

    public function deleteAttachment($attachmentId)
    {
        $decodedAttachmentId = decode_id($attachmentId);
        $attachmentModel = new \App\Models\AttachmentModel();
        $attachment = $attachmentModel->find($decodedAttachmentId);
        if (!$attachment) {
            session()->setFlashdata('error', 'Lampiran tidak ditemukan.');
            return redirect()->back();
        }

        $filePath = FCPATH . 'uploads/' . $attachment['file_name'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $attachmentModel->delete($decodedAttachmentId);
        session()->setFlashdata('success', 'Lampiran berhasil dihapus.');
        return redirect()->back();
    }
}
