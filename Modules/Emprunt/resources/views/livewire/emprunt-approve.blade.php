            <!-- Corps -->
            <div class="px-4 py-4 sm:px-6 sm:py-5">
                <form wire:submit="approve" class="space-y-5">
                    <!-- Info -->
                    <div class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-3">
                        <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
                        <p class="text-sm text-blue-700">
                            {{ __('Please enter the actual return date and add a note before approving this loan.') }}
                        </p>
                    </div>

                    <!-- Date de retour réelle -->
                    <div>
                        <label for="date_retour_reelle" class="block text-sm font-medium text-gray-700">
                            {{ __('Actual return date') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-calendar-check text-sm text-gray-400"></i>
                            </div>
                            <input
                                id="date_retour_reelle"
                                type="date"
                                wire:model="date_retour_souhaitee"
                                class="block w-full rounded-lg border-gray-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                            >
                        </div>
                        @error('date_retour_reelle')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Note du bibliothécaire -->
                    <div>
                        <label for="note_bibliothecaire" class="block text-sm font-medium text-gray-700">
                            {{ __('Librarian note') }} <span class="text-xs text-gray-400">{{ __('(Optional)') }}</span>
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
                            wire:click="requestCancelApprove"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
                        >
                            {{ __('Cancel') }}
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="approve"
                            class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-70 sm:w-auto"
                        >
                            <span wire:loading.remove wire:target="approve">
                                <i class="fas fa-check mr-2"></i>
                                {{ __('Approve') }}
                            </span>
                            <span wire:loading wire:target="approve">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                {{ __('Approving...') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>