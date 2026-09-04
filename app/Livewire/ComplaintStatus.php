<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ComplaintStatus extends Component
{
    public $ticketNumber;
    public $complaint = null;
    public $searched = false;
    public $errorMessage = '';

    public function search()
    {
        $this->validate([
            'ticketNumber' => 'required|string',
        ]);

        $this->searched = true;
        $this->errorMessage = '';
        $this->complaint = null;

        try {
            $apiUrl = env('PENGADUAN_API_URL', 'http://127.0.0.1:9000/api/v1/complaints');
            // Assuming the API provides a GET endpoint for the ticket number
            $response = Http::get("{$apiUrl}/{$this->ticketNumber}");
            
            if ($response->successful() && $response->json('success')) {
                // Map the API response to the format expected by the view
                // This might need adjustment based on the actual API response structure
                $this->complaint = json_decode(json_encode($response->json('data'))); 
            } else {
                $this->errorMessage = 'Tiket pengaduan tidak ditemukan atau terjadi kesalahan pada server.';
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Gagal terhubung ke server pengaduan.';
        }
    }

    public function resetSearch()
    {
        $this->complaint = null;
        $this->searched = false;
        $this->ticketNumber = '';
        $this->errorMessage = '';
    }

    public function render()
    {
        return view('livewire.complaint-status')
            ->layout('components.layouts.app', ['title' => 'Cek Status Pengaduan']);
    }
}
