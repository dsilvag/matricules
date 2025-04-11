<!-- resources/views/filament/forms/components/dwelling-search.blade.php -->

<div class="space-y-4">
    <x-filament::forms::field-wrapper :field="$field">
        <!-- Componente select con las opciones obtenidas desde el campo -->
        <x-filament::forms::select
            wire:model="{{ $field->getStatePath() }}"
            :options="$field->getDwellingOptions()" 
            label="{{ $field->label }}"
            placeholder="Seleccione un habitatge"
            :searchable="true"  
            :disable="false"  
        />
    </x-filament::forms::field-wrapper>
</div>
