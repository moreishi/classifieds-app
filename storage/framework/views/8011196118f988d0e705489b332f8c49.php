<div>
    <div class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Listings</h1>
                <p class="text-sm text-gray-500 mt-1">Manage your active and sold listings.</p>
            </div>
            <a href="<?php echo e(route('listings.create')); ?>"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                + New Listing
            </a>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('message')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?php echo e(session('message')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($listings->isEmpty()): ?>
            <div class="text-center py-16 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-lg">No listings yet</p>
                <p class="text-gray-400 text-sm mt-1">Create your first listing to get started!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        
                        <div class="h-40 bg-gray-100 relative">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($listing->getFirstMediaUrl('photos', 'card')): ?>
                                <img src="<?php echo e($listing->getFirstMediaUrl('photos', 'card')); ?>"
                                     alt="<?php echo e($listing->title); ?>"
                                     class="w-full h-full object-cover" />
                            <?php else: ?>
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </div>

                        
                        <div class="p-4">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-semibold text-gray-900 truncate"><?php echo e($listing->title); ?></h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($listing->status === 'sold'): ?>
                                    <span class="shrink-0 px-2 py-0.5 bg-red-600 text-white text-xs font-medium rounded-full">Sold</span>
                                <?php else: ?>
                                    <span class="shrink-0 px-2 py-0.5 bg-green-600 text-white text-xs font-medium rounded-full">Active</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">₱<?php echo e(number_format($listing->price / 100)); ?></p>
                            <p class="text-xs text-gray-400 mt-1"><?php echo e($listing->city?->name ?? '—'); ?></p>

                            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                                <a href="<?php echo e(route('listing.show', $listing->slug)); ?>"
                                   class="text-sm text-blue-600 hover:text-blue-800">
                                    View
                                </a>
                                <span class="text-gray-300">|</span>
                                <a href="<?php echo e(route('listings.edit', $listing->slug)); ?>"
                                   class="text-sm text-blue-600 hover:text-blue-800">
                                    Edit
                                </a>
                                <span class="text-gray-300">|</span>
                                <span class="text-xs text-gray-400"><?php echo e($listing->created_at->diffForHumans()); ?></span>
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <div class="mt-6">
                <?php echo e($listings->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /home/kc/projects/classifieds-app/resources/views/livewire/my-listings.blade.php ENDPATH**/ ?>