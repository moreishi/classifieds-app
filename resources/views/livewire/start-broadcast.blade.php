<div>
    {{-- Global modal overlay --}}
    <div x-data="broadcastModal()"
         x-on:open-broadcast-modal.window="open()"
         x-on:keydown.escape.window="close()"
         x-show="isOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"
             wire:click.self="close"
             x-on:click="close()"></div>

        {{-- Modal content --}}
        <div class="relative bg-gray-900 rounded-2xl w-full max-w-md mx-4 shadow-2xl border border-gray-700 flex flex-col max-h-[90vh]"
             x-show="isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            {{-- Close button --}}
            <button wire:click="close" x-on:click="close()"
                    class="absolute top-3 right-3 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Scrollable content --}}
            <div class="overflow-y-auto flex-1 rounded-2xl">

            {{-- Step 1: Capture --}}
            @if($step === 'capture')
                <div class="relative">
                    {{-- Camera viewfinder --}}
                    <div class="relative bg-black aspect-[3/4] flex items-center justify-center overflow-hidden">
                        <video x-ref="video" autoplay playsinline
                               class="w-full h-full object-cover"
                               x-on:play="document.getElementById('canvas-{{ $this->getId() }}')?.setAttribute('width', $el.videoWidth); document.getElementById('canvas-{{ $this->getId() }}')?.setAttribute('height', $el.videoHeight)">
                        </video>

                        {{-- Camera overlay --}}
                        <div class="absolute inset-0 border-[3px] border-white/20 rounded-[1.75rem] m-4 pointer-events-none"></div>

                        {{-- GPS status --}}
                        <div class="absolute top-4 left-4 flex items-center gap-2 px-3 py-1.5 rounded-full bg-black/60 text-white text-xs">
                            @if($gpsStatus === 'detecting')
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-neon-green opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-neon-green"></span>
                                </span>
                                Detecting location...
                            @elseif($gpsStatus === 'captured')
                                <span class="w-2 h-2 rounded-full bg-neon-green"></span>
                                Location found
                            @elseif($gpsStatus === 'denied' || $gpsStatus === 'unavailable')
                                <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                                Location unavailable
                            @endif
                        </div>

                        {{-- Error message --}}
                        @if($error)
                            <div class="absolute bottom-20 left-4 right-4 bg-red-500/90 text-white text-xs px-3 py-2 rounded-lg">
                                {{ $error }}
                            </div>
                        @endif
                    </div>

                    {{-- Controls --}}
                    <div class="p-4 bg-gray-900">
                        <div class="flex items-center justify-center gap-6">
                            {{-- Gallery picker --}}
                            <label class="flex flex-col items-center gap-1 text-gray-400 cursor-pointer hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs">Gallery</span>
                                <input type="file" accept="image/*" class="hidden"
                                       x-on:change="
                                           const file = $event.target.files[0];
                                           if (!file) return;
                                           capturing = true;
                                           const reader = new FileReader();
                                           reader.onload = (e) => {
                                               if (typeof this.$wire !== 'undefined') {
                                                   this.$wire.setPhoto(e.target.result).finally(() => { capturing = false; });
                                               } else { capturing = false; }
                                           };
                                           reader.onerror = () => { capturing = false; };
                                           reader.readAsDataURL(file);
                                           $event.target.value = '';
                                       ">
                            </label>

                            {{-- Capture button --}}
                            <button x-on:click="capture()"
                                    class="w-16 h-16 rounded-full border-4 border-white flex items-center justify-center hover:scale-105 transition-transform">
                                <span class="w-14 h-14 rounded-full bg-white"></span>
                            </button>

                            {{-- Spacer for symmetry --}}
                            <div class="w-16"></div>
                        </div>
                    </div>

                    {{-- Capturing spinner --}}
                    <div x-show="capturing"
                         class="absolute inset-0 z-20 bg-black/80 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-12 h-12 border-[3px] border-neon-pink border-t-transparent rounded-full animate-spin mx-auto"></div>
                            <p class="text-white text-sm mt-3 font-medium">Processing photo...</p>
                        </div>
                    </div>

                    {{-- Hidden canvas for capture --}}
                    <canvas id="canvas-{{ $this->getId() }}" class="hidden"></canvas>
                </div>

            {{-- Step 2: Compose --}}
            @elseif($step === 'compose')
                <div class="p-5 space-y-4">
                    <h3 class="text-lg font-bold text-white">Share what you're selling</h3>

                    {{-- Photo thumbnail --}}
                    <div class="relative rounded-xl overflow-hidden aspect-[4/3] bg-gray-800">
                        @if($capturedPhoto)
                            <img src="{{ $capturedPhoto }}" class="w-full h-full object-cover">
                        @endif
                        <button wire:click="$set('step', 'capture')"
                                class="absolute top-2 left-2 px-3 py-1 rounded-full bg-black/60 text-white text-xs hover:bg-black/80 transition-colors">
                            Retake
                        </button>
                    </div>

                    {{-- Description --}}
                    <div>
                        <textarea wire:model="description"
                                  placeholder="What are you selling? E.g., Fresh lumpia, assorted flavors, 3 for ₱50"
                                  class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white text-sm placeholder-gray-500 focus:border-neon-pink focus:ring-1 focus:ring-neon-pink outline-none resize-none"
                                  rows="3" maxlength="200"></textarea>
                        <div class="text-right text-xs text-gray-500 mt-1">{{ strlen($description) }}/200</div>
                        @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Location + Map preview --}}
                    <div>
                        <div class="flex items-center gap-2 text-sm">
                            @if($gpsStatus === 'captured' && $locationName)
                                <span class="text-neon-green">📍 {{ $locationName }}</span>
                            @elseif($gpsStatus === 'captured')
                                <span class="text-neon-green">📍 Location detected</span>
                            @elseif($gpsStatus === 'denied')
                            <div class="flex-1">
                                <label class="text-gray-400 text-xs block mb-1">Select your location</label>
                                <select wire:model="cityId"
                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:border-neon-pink outline-none">
                                    <option value="">Choose city/municipality</option>
                                    @foreach($provinces as $province)
                                        <optgroup label="{{ $province->name }}">
                                            @foreach($province->children as $city)
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <span class="text-yellow-400">⏳ Detecting location...</span>
                        @endif
                    </div>

                    {{-- Map preview in compose --}}
                    @if($gpsStatus === 'captured' && $latitude && $longitude)
                        <div class="mt-2 rounded-xl overflow-hidden border border-gray-700 relative" style="height: 160px;">
                            <div id="compose-map-{{ $this->getId() }}"
                                 class="h-full w-full"
                                 x-init="setTimeout(() => initComposeMap({{ $latitude }}, {{ $longitude }}), 300)"></div>
                            {{-- Fixed center pin --}}
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none text-3xl drop-shadow-lg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#e11d48"/>
                                    <circle cx="12" cy="9" r="4.5" fill="white"/>
                                </svg>
                            </div>
                            <p class="absolute bottom-1 left-1/2 -translate-x-1/2 z-10 text-[10px] text-white bg-black/60 px-2 py-0.5 rounded-full whitespace-nowrap">Drag map to adjust location</p>
                        </div>
                    @endif
                    </div>

                    {{-- Error --}}
                    @if($error)
                        <div class="bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-xl text-sm">
                            {{ $error }}
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button wire:click="close" x-on:click="close()"
                                class="flex-1 px-4 py-3 rounded-xl border border-gray-700 text-gray-300 text-sm font-medium hover:bg-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="startBroadcast" wire:loading.attr="disabled"
                                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-neon-pink to-neon-purple text-white text-sm font-bold hover:shadow-lg hover:shadow-neon-pink/30 transition-all duration-300 disabled:opacity-50">
                            <span wire:loading.remove>Go Live</span>
                            <span wire:loading>Starting...</span>
                        </button>
                    </div>
                </div>

            {{-- Step 3: Done / Confirmation --}}
            @elseif($step === 'done')
                <div class="p-8 text-center space-y-5">
                    {{-- Success animation --}}
                    <div class="flex justify-center">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-neon-pink to-neon-purple flex items-center justify-center animate-scale-in">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-white">You're live!</h3>
                        <p class="text-gray-400 text-sm mt-1">Buyers in your area can now see your beacon.</p>
                    </div>

                    {{-- Active beacon info --}}
                    @if($activeBeacon)
                        <div class="bg-gray-800 rounded-xl p-4 text-left flex items-center gap-3">
                            <span class="relative flex h-3 w-3 flex-shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-neon-pink opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-neon-pink"></span>
                            </span>
                            <div class="min-w-0">
                                <p class="text-white text-sm font-medium truncate">{{ $activeBeacon->description }}</p>
                                <p class="text-gray-500 text-xs">Selling now</p>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button wire:click="endBroadcast" x-on:click="close()"
                                class="flex-1 px-4 py-3 rounded-xl border border-red-500/50 text-red-400 text-sm font-medium hover:bg-red-500/10 transition-colors">
                            End Broadcast
                        </button>
                        <button wire:click="goToFeed"
                                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-neon-pink to-neon-purple text-white text-sm font-bold hover:shadow-lg hover:shadow-neon-pink/30 transition-all duration-300">
                            View Feed
                        </button>
                    </div>
                </div>
            @endif
            </div>{{-- /scrollable --}}
        </div>
    </div>

    {{-- Alpine.js component --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('broadcastModal', () => ({
                isOpen: false,
                videoStream: null,
                capturing: false,
                composeMap: null,

                open() {
                    this.isOpen = true;
                    this.$nextTick(() => {
                        this.startCamera();
                        this.detectGps();
                    });
                },

                close() {
                    this.destroyComposeMap();
                    this.stopCamera();
                    if (typeof this.$wire !== 'undefined') {
                        this.$wire.close();
                    }
                    this.isOpen = false;
                },

                async startCamera() {
                    const video = this.$refs.video;
                    if (!video) return;

                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'environment', width: { ideal: 1080 }, height: { ideal: 1920 } },
                            audio: false,
                        });
                        video.srcObject = stream;
                        this.videoStream = stream;
                    } catch (e) {
                        if (typeof this.$wire !== 'undefined') {
                            this.$wire.error = 'Camera access denied. Please upload from gallery instead.';
                        }
                    }
                },

                capture() {
                    const video = this.$refs.video;
                    if (!video || this.capturing) return;

                    const canvas = document.getElementById('canvas-{{ $this->getId() }}');
                    if (!canvas) return;

                    this.capturing = true;

                    canvas.width = video.videoWidth || 1080;
                    canvas.height = video.videoHeight || 1920;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

                    if (typeof this.$wire !== 'undefined') {
                        this.$wire.setPhoto(dataUrl).finally(() => {
                            this.capturing = false;
                        });
                    } else {
                        this.capturing = false;
                    }
                },

                initComposeMap(lat, lng) {
                    if (this.composeMap) return;
                    if (typeof L === 'undefined') {
                        setTimeout(() => this.initComposeMap(lat, lng), 300);
                        return;
                    }
                    const id = 'compose-map-{{ $this->getId() }}';
                    this.$nextTick(() => {
                        const el = document.getElementById(id);
                        if (!el) return;
                        this.composeMap = L.map(el, {
                            center: [lat, lng],
                            zoom: 16,
                            zoomControl: true,
                            attributionControl: false,
                        });
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(this.composeMap);
                        // Update Livewire coords when map is panned/zoomed
                        this.composeMap.on('moveend', () => {
                            const c = this.composeMap.getCenter();
                            if (typeof this.$wire !== 'undefined') {
                                this.$wire.latitude = c.lat.toFixed(7);
                                this.$wire.longitude = c.lng.toFixed(7);
                            }
                        });
                        setTimeout(() => this.composeMap?.invalidateSize(), 200);
                    });
                },

                destroyComposeMap() {
                    if (this.composeMap) { this.composeMap.remove(); this.composeMap = null; }
                },

                detectGps() {
                    if (!navigator.geolocation) {
                        if (typeof this.$wire !== 'undefined') {
                            this.$wire.gpsUnavailable();
                        }
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;
                            const name = `📍 ${lat.toFixed(4)}, ${lng.toFixed(4)}`;

                            // Try reverse geocode via free API
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&accept-language=en`)
                                .then(r => r.json())
                                .then(data => {
                                    const city = data.address?.city || data.address?.town || data.address?.municipality || data.address?.county || '';
                                    const displayName = city ? `📍 ${city}` : name;
                                    if (typeof this.$wire !== 'undefined') {
                                        this.$wire.captureGps(lat, lng, displayName);
                                    }
                                })
                                .catch(() => {
                                    if (typeof this.$wire !== 'undefined') {
                                        this.$wire.captureGps(lat, lng, name);
                                    }
                                });
                        },
                        () => {
                            if (typeof this.$wire !== 'undefined') {
                                this.$wire.gpsDenied();
                            }
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                },

                stopCamera() {
                    if (this.videoStream) {
                        this.videoStream.getTracks().forEach(t => t.stop());
                        this.videoStream = null;
                    }
                    const video = this.$refs?.video;
                    if (video) {
                        video.srcObject = null;
                    }
                }
            }));
        });
    </script>
</div>
