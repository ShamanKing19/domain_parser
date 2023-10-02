@component($typeForm, get_defined_vars())
    @php
        $attrName = $attributes['name'];
        $count = 0;
        $inputId = \Illuminate\Support\Str::random(20);
    @endphp
    <div class="multi__container">
        <ul class="multi__input">
            @if(count($columns))
                @foreach($columns as $val)
                    @php
                        if(isset($val['id'])) {
                            $attributes['name'] = $attrName.'['.$val['id'].'][value]';
                        } else {
                            $attributes['name'] = $attrName.'[n'.$count.'][value]';
                            $count++;
                        }

                        $attributes['value'] = $val['value'];
                        $attributes['id'] = $inputId;
                    @endphp
                    <li data-controller="input" data-input-mask="{{ $mask ?? '' }}">
                        <input data-first-name="{{ $attrName  }}" data-input-count="{{ $count }}" {{ $attributes }}>
                        <button class="multi__input--button__delete" type="button">Удалить</button>
                        @isset($val['id'])
                            <input type="hidden" name="{{ $attrName.'['.$val['id'].'][id]' }}" value="{{ $val['id'] }}">
                        @endisset
                    </li>
                @endforeach
            @else
                @php
                    $attributes['name'] = $attrName.'[n1][value]';
                @endphp
                <li data-controller="input" data-input-mask="{{$mask ?? ''}}">
                    <input data-first-name="{{ $attrName  }}" data-input-count="1" {{ $attributes }}>
                    <button class="multi__input--button__delete" type="button">Удалить</button>
                </li>
            @endif

            @empty(!$datalist)
                <datalist id="datalist-{{$name}}">
                    @foreach($datalist as $item)
                        <option value="{{ $item }}">
                    @endforeach
                </datalist>
            @endempty
        </ul>
        <button class="multi__add btn btn-link" type="button" data-id="{{ $inputId }}">Добавить</button>
    </div>
@endcomponent

<script>
    document.addEventListener('click', function(event) {
        const button = event.target;
        if(!button.classList.contains('multi__add')) {
            return;
        }

        if(button.dataset.id !== '{{ $inputId }}') {
            return;
        }

        const mainContainer = button.closest('.multi__container');
        const mainDiv = mainContainer.querySelector('.multi__input');
        if(!mainDiv) {
            return;
        }

        const lastChildDiv = mainDiv.querySelector('li:last-child');
        if(!lastChildDiv) {
            return;
        }

        const newDivWithInput = lastChildDiv.cloneNode(true);
        mainDiv.appendChild(newDivWithInput);

        const newInput = newDivWithInput.querySelector('input');
        newInput.value = '';
        newInput.setAttribute('value', '');
        const inputFirstName = newInput.getAttribute('data-first-name');
        const inputLastId = newInput.getAttribute('data-input-count');
        const newLastId = parseInt(inputLastId) + 1;
        const lastIdName = 'n' + newLastId;
        newInput.setAttribute('name', inputFirstName + '[' + lastIdName + '][value]');
        newInput.setAttribute('data-input-count', newLastId);

        const hiddenInput = newDivWithInput.querySelector('input[type="hidden"]');
        if(hiddenInput) {
            hiddenInput.remove();
        }
    });

    document.addEventListener('click', function(event) {
        const button = event.target;
        if(!button.classList.contains('multi__input--button__delete')) {
            return;
        }

        const buttons = document.querySelectorAll('.multi__input--button__delete');
        const wrapper = button.parentElement;
        if(buttons.length > 1) {
            wrapper.remove();
            return;
        }

        const valueInput = wrapper.querySelector('input');
        valueInput.value = '';
    });
</script>

<style>
    .multi__input {
        padding: 0;
    }

    .multi__input li {
        margin-bottom: 5px;
        padding: 0;
        list-style-type: none;
        display: flex;
    }

    .multi__input--button__delete {
        margin-left: .5rem;
        padding: .5rem .75rem;
        border: 1px solid rgba(21,20,26,.1);
        background-color: #ffffff;
    }
</style>
