<div>
    <x-action-section>
        <x-slot name="title">{{ __('Delete Account') }}</x-slot>
        <x-slot name="description">{{ __('Permanently delete your account.') }}</x-slot>
        
        <x-slot name="content">
            <div class="max-w-xl text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please download any information you wish to keep.') }}
            </div>
            
            <div class="mt-5">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Delete Account') }}
            </x-danger-button>
            </div>
            
            <!-- Modal -->
            <x-dialog-modal wire:model.live="confirmingUserDeletion">
                <x-slot name="title">{{ __('Delete Account') }}</x-slot>
                
                <x-slot name="content">
                    {{ __('Are you sure you want to delete your account? This action is irreversible.') }}
                </x-slot>
                
                <x-slot name="footer">
                    <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-danger-button wire:click="deleteUser" wire:loading.attr="disabled">
                        {{ __('Delete Account') }}
                    </x-danger-button>
                </x-slot>
            </x-dialog-modal>
        </x-slot>
    </x-action-section>
</div>
