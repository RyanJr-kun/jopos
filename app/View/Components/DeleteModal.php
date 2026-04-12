<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DeleteModal extends Component
{
    public function __construct(
        public string $message = 'Apakah Anda yakin ingin menghapus item ini?',
        public string $itemTitleId = 'delete-item-title',
        public string $confirmBtnId = 'confirmDeleteBtn',
        public string $modalId = 'deleteModal',
    ) {}

    public function render()
    {
        return view('components.delete-modal');
    }
}
