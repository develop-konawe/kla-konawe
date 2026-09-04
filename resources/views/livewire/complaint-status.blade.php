<div>
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 pt-32 pb-20 text-white text-center relative overflow-hidden">
        <div class="h-24"></div>
        <div class="absolute top-8 left-16 w-24 h-24 bg-primary-400/20 rounded-full blur-2xl"></div>
        <div class="absolute bottom-8 right-16 w-40 h-40 bg-secondary-400/10 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Cek Status Pengaduan</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto font-medium">Pantau perkembangan tindak lanjut dari laporan yang telah Anda sampaikan.</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-8 relative z-20">
        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100 mb-8">
            <form wire:submit="search" class="flex flex-col md:flex-row gap-4">
                <input type="text" wire:model="ticketNumber" placeholder="Masukkan Nomor Tiket (Contoh: TKT-20231010-ABCD)" class="flex-grow rounded-xl border-2 border-gray-200 bg-gray-50 focus:bg-white focus:border-gray-800 focus:ring-gray-800 py-4 px-6 font-medium text-lg transition" required>
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-4 px-8 rounded-xl shadow-md transition whitespace-nowrap flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Cek Status
                </button>
            </form>
            @error('ticketNumber') <span class="text-red-500 font-bold text-sm mt-2 block">{{ $message }}</span> @enderror
        </div>

        @if($searched)
            @if($complaint)
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8 bg-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-100">
                        <div>
                            <span class="block text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Nomor Tiket</span>
                            <span class="text-2xl font-black font-mono text-gray-900">{{ $complaint->ticket_number }}</span>
                        </div>
                        <div>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'resolved' => 'bg-green-100 text-green-800 border-green-200',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                                $statusLabels = [
                                    'pending' => 'Menunggu Tindak Lanjut',
                                    'in_progress' => 'Sedang Diproses',
                                    'resolved' => 'Selesai',
                                    'rejected' => 'Ditolak',
                                ];
                                $statusVal = is_string($complaint->status) ? $complaint->status : ($complaint->status->value ?? 'pending');
                                $color = $statusColors[$statusVal] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                $label = $statusLabels[$statusVal] ?? 'Menunggu';
                            @endphp
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold border {{ $color }}">
                                {{ $label }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <h3 class="text-lg font-black text-gray-900 mb-6">Riwayat Tindak Lanjut</h3>
                        
                        @php
                            $followups = $complaint->follow_ups ?? $complaint->followUps ?? [];
                        @endphp
                        @if((is_array($followups) || is_object($followups)) && count((array)$followups) > 0)
                            <div class="relative border-l-2 border-primary-200 ml-4 space-y-8">
                                @foreach($followups as $followup)
                                <div class="ml-8 relative">
                                    <span class="absolute -left-[2.55rem] flex items-center justify-center w-6 h-6 bg-primary-500 rounded-full ring-4 ring-white shadow-sm">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    </span>
                                    <div class="bg-gray-50 p-5 rounded-2xl">
                                        <h4 class="font-bold text-gray-900 mb-1">{{ ucfirst($followup->action_type ?? 'Tindak Lanjut') }}</h4>
                                        <time class="block text-sm text-gray-400 font-medium mb-3">
                                            {{ isset($followup->created_at) ? \Carbon\Carbon::parse($followup->created_at)->format('d M Y, H:i') : 'Baru saja' }}
                                        </time>
                                        <p class="text-gray-600 font-medium">{{ $followup->note ?? '-' }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 bg-gray-50 rounded-2xl">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-gray-500 font-bold">Laporan Anda telah kami terima.</p>
                                <p class="text-gray-400 font-medium text-sm mt-1">Sedang menunggu antrean peninjauan oleh petugas kami.</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-6 text-center">
                    <button wire:click="resetSearch" class="text-gray-500 hover:text-gray-700 font-bold text-sm underline decoration-2 underline-offset-4">Cari Tiket Lain</button>
                </div>
            @else
                <div class="bg-white border-t-4 border-red-400 p-10 rounded-3xl shadow-xl text-center">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 text-red-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">Pencarian Gagal</h3>
                    <p class="text-gray-500 font-medium">{{ $errorMessage ?: 'Pastikan nomor tiket yang Anda masukkan sudah benar dan sesuai.' }}</p>
                </div>
            @endif
        @endif
    </div>
</div>
