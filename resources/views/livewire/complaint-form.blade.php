<div>
    <div class="bg-gradient-to-r from-red-600 to-red-800 pt-32 pb-20 text-white text-center relative overflow-hidden">
        <div class="h-24"></div>
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Lapor & Pengaduan</h1>
            <p class="text-xl text-red-100 max-w-2xl mx-auto font-medium">Kanal aman untuk melaporkan masalah atau pelanggaran hak anak. Laporan Anda akan ditindaklanjuti secara rahasia.</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 -mt-10 relative z-20">
        @if($isSubmitted)
            <div class="bg-white border-t-8 border-green-500 p-10 rounded-3xl shadow-xl text-center">
                <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-3xl font-black text-gray-900 mb-2">Laporan Berhasil Diterima!</h2>
                <p class="text-gray-600 mb-8 font-medium text-lg">Tim khusus kami akan segera meninjau dan menindaklanjuti laporan ini dengan memprioritaskan kerahasiaan Anda.</p>
                <div class="bg-red-50 p-6 rounded-2xl inline-block border border-red-100 mb-8 shadow-inner">
                    <span class="block text-sm font-bold text-gray-500 mb-2 uppercase tracking-wider">Simpan Nomor Tiket Anda:</span>
                    <span class="text-4xl font-mono font-black text-red-600">{{ $ticketNumber }}</span>
                    <p class="text-sm text-gray-500 font-medium mt-3 max-w-xs mx-auto">Gunakan nomor tiket ini untuk mengecek status tindak lanjut pengaduan Anda nanti.</p>
                </div>
                <div>
                    <a href="/cek-pengaduan" class="inline-flex items-center bg-gray-900 hover:bg-gray-800 text-white font-bold py-4 px-8 rounded-full shadow-lg hover-lift transition">
                        <span>Cek Status Laporan</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        @else
            <form wire:submit="submit" class="bg-white p-8 md:p-12 rounded-3xl shadow-xl border border-gray-100 relative">
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-10 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 font-bold">
                                Jangan ragu untuk melapor. Kami menjamin kerahasiaan identitas Anda jika Anda memilih opsi Anonim.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Identitas Pelapor -->
                <div class="mb-12">
                    <h3 class="text-2xl font-black text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-sm mr-3">1</span>
                        Identitas Pelapor
                    </h3>
                    
                    <div class="mb-8">
                        <label class="flex items-center p-5 border-2 rounded-2xl cursor-pointer transition-all duration-300 {{ $is_anonymous ? 'bg-red-50 border-red-300 shadow-md' : 'hover:bg-gray-50 border-gray-200' }}">
                            <input type="checkbox" wire:model.live="is_anonymous" class="text-red-600 focus:ring-red-500 rounded w-6 h-6 border-gray-300">
                            <div class="ml-4">
                                <span class="block font-black text-gray-900 text-lg">Kirim sebagai Anonim (Rahasia)</span>
                                <span class="block text-sm text-gray-500 font-medium mt-1">Identitas Anda akan disembunyikan dan tidak akan ditampilkan pada laporan akhir.</span>
                            </div>
                        </label>
                    </div>

                    @if(!$is_anonymous)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="reporter_name" class="w-full rounded-xl border-gray-300 p-3 bg-gray-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition">
                            @error('reporter_name') <span class="text-red-500 font-bold text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                            <input type="email" wire:model="reporter_email" class="w-full rounded-xl border-gray-300 p-3 bg-gray-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition">
                            @error('reporter_email') <span class="text-red-500 font-bold text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nomor Telepon / WhatsApp <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="reporter_phone" class="w-full rounded-xl border-gray-300 p-3 bg-gray-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition">
                            @error('reporter_phone') <span class="text-red-500 font-bold text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @endif


                </div>

                <hr class="border-gray-100 mb-10">

                <!-- Detail Kejadian -->
                <div class="mb-12">
                    <h3 class="text-2xl font-black text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-sm mr-3">2</span>
                        Detail Kejadian
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Judul Pengaduan <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="title" placeholder="Contoh: Fasilitas umum rusak, dsb." class="w-full rounded-xl border-gray-300 p-3 bg-gray-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition">
                            @error('title') <span class="text-red-500 font-bold text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Detail Pengaduan <span class="text-red-500">*</span></label>
                            <textarea wire:model="chronology" rows="6" placeholder="Ceritakan pengaduan Anda secara rinci..." class="w-full rounded-xl border-gray-300 p-3 bg-gray-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition resize-none"></textarea>
                            @error('chronology') <span class="text-red-500 font-bold text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Lampiran Bukti (Foto/Dokumen, Opsional)</label>
                            <input type="file" wire:model="attachment" class="w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-red-100 file:text-red-700 hover:file:bg-red-200 cursor-pointer transition">
                            <p class="text-sm text-gray-500 mt-3 font-medium">Maksimal 5MB. Bukti Anda akan disimpan secara terenkripsi dan dijaga kerahasiaannya.</p>
                            @error('attachment') <span class="text-red-500 font-bold text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Persetujuan -->
                <div class="mb-10 bg-red-50/50 p-6 rounded-2xl border border-red-100">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" wire:model="consent" class="text-red-600 focus:ring-red-500 rounded w-5 h-5 mt-1 border-gray-300">
                        <span class="ml-3 text-sm text-gray-700 font-medium leading-relaxed">
                            Saya menyatakan bahwa informasi yang saya berikan adalah benar sesuai pengetahuan saya. Saya memahami bahwa laporan ini akan ditindaklanjuti oleh pihak berwenang sesuai prosedur hukum yang berlaku.
                        </span>
                    </label>
                    @error('consent') <span class="text-red-500 font-bold text-sm mt-2 block">{{ $message }}</span> @enderror
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-black py-5 px-8 rounded-2xl shadow-lg shadow-red-600/30 hover-lift flex justify-center items-center text-lg">
                        <span wire:loading.remove>Kirim Laporan Pengaduan</span>
                        <span wire:loading>Memproses Laporan Rahasia Anda...</span>
                    </button>
                </div>
                @error('submit')
                <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg font-medium text-center">
                    {{ $message }}
                </div>
                @enderror
            </form>
        @endif
    </div>
</div>
