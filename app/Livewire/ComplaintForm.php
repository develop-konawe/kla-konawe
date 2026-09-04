<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;

class ComplaintForm extends Component
{
    use WithFileUploads;

    public $title;
    public $reporter_name;
    public $reporter_email;
    public $reporter_phone;
    public $is_anonymous = true;
    public $chronology;
    public $attachment;
    public $consent = false;

    public $isSubmitted = false;
    public $ticketNumber = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'reporter_name' => 'required_if:is_anonymous,false|max:255',
        'reporter_email' => 'nullable|email|max:255',
        'reporter_phone' => 'required_if:is_anonymous,false|max:255',
        'chronology' => 'required|string|min:10',
        'attachment' => 'nullable|file|max:5120', // 5MB max
        'consent' => 'accepted'
    ];

    public function submit()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'body' => $this->chronology,
            'complaint_category_id' => '1',
            'is_anonymous' => $this->is_anonymous ? 'true' : 'false',
            'is_confidential' => 'true',
            'reporter_name' => $this->is_anonymous ? 'Anonim' : ($this->reporter_name ?? ''),
            'reporter_email' => $this->is_anonymous ? '' : ($this->reporter_email ?? ''),
            'reporter_phone' => $this->is_anonymous ? '' : ($this->reporter_phone ?? ''),
            'organization_unit_id' => '',
            'service_id' => '',
        ];

        $http = Http::asMultipart();

        if ($this->attachment) {
            $http->attach(
                'attachments', 
                file_get_contents($this->attachment->getRealPath()), 
                $this->attachment->getClientOriginalName()
            );
        }

        try {
            $apiUrl = env('PENGADUAN_API_URL', 'http://127.0.0.1:9000/api/v1/complaints');
            $response = $http->post($apiUrl, $data);
            
            if ($response->successful() && $response->json('success')) {
                $this->ticketNumber = $response->json('data.ticket_number') ?? 'TKT-SUCCESS';
                $this->isSubmitted = true;
                $this->reset(['title', 'reporter_name', 'reporter_email', 'reporter_phone', 'chronology', 'attachment', 'consent']);
            } else {
                $this->addError('submit', 'Gagal mengirim pengaduan. Pastikan server API tersedia.');
            }
        } catch (\Exception $e) {
            $this->addError('submit', 'Terjadi kesalahan koneksi saat mengirim pengaduan.');
        }
    }

    public function render()
    {
        return view('livewire.complaint-form')
            ->layout('components.layouts.app', ['title' => 'Formulir Pengaduan Publik']);
    }
}
