            <!-- Corps -->
            <div class="px-4 py-4 sm:px-6 sm:py-5">
                <form wire:submit="refuse" class="space-y-5">

                    <!-- Note du bibliothécaire Rqison du refus -->
                    <div>
                        <label for="note_bibliothecaire" class="block text-sm font-medium text-gray-700">
                            {{ __('Librarian note') }} <span class="text-xs text-red-700">*</span>
                        </label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute top-3 left-3">
                                <i class="fas fa-comment text-sm text-gray-400"></i>
                            </div>
                            <textarea
                                id="note_bibliothecaire"
                                wire:model="note_bibliothecaire"
                                rows="3"
                                placeholder="{{ __('Add a note about the return condition, delay, etc...') }}"
                                class="block w-full rounded-lg border-gray-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0 resize-y"
                            ></textarea>
                        </div>
                        @error('note_bibliothecaire')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col-reverse gap-2 border-t border-gray-200 pt-4 sm:flex-row sm:items-center sm:justify-end">
                        <button
                            type="button"
                            wire:click="requestCancelRefuse"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
                        >
                            {{ __('Cancel') }}
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="refuse"
                            class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-70 sm:w-auto"
                        >
                            <span wire:loading.remove wire:target="refuse">
                                <i class="fas fa-times mr-2"></i>
                                {{ __('Refuse') }}
                            </span>
                            <span wire:loading wire:target="refuse">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                {{ __('Refusing...') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>