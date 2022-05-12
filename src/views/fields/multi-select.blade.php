<div class="relative"
     x-data="initMultiSelect_{{ $field->id() }}()"
     x-init="$refs.dropdown.classList.remove('hidden')"
>

    <span class="inline-block w-full rounded-md shadow-sm">
      <button @click.stop="open=!open" type="button" aria-haspopup="listbox" aria-expanded="true" aria-labelledby="listbox-label" class="cursor-pointer relative w-full rounded-md border border-gray-300 bg-white pl-3 pr-10 py-2 text-left focus:outline-none focus:shadow-outline-purple focus:border-purple transition ease-in-out duration-150 sm:text-sm sm:leading-5">
        <div class="flex items-center space-x-3">
            <input type="hidden" name="{{ $field->name()}}" :value="currentId" value="{{ $field->formViewValue($item) }}" />

            <img v-show="currentImage" :src="currentImage" src="" :alt="currentName" alt="" :class="currentImage == '' ? 'hidden' : ''" class="flex-shrink-0 h-6 w-6 rounded-full">

            <span class="block truncate" x-text="currentName"></span>
        </div>
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="none" stroke="currentColor">
            <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </span>
      </button>
    </span>

    <div @click.outside="open = false" :class="open ? '' : 'hidden'" x-ref="dropdown" class="absolute mt-1 w-full rounded-md bg-white shadow-lg hidden z-10">
        <input placeholder="Поиск" type="text" x-on:keydown="filterOptions()" x-model="search" value="" class="bg-white focus:outline-none focus:shadow-outline rounded-lg py-2 px-4 block w-full appearance-none leading-norma" />

        <ul x-ref="ul" tabindex="-1" role="listbox" aria-labelledby="listbox-label" aria-activedescendant="listbox-item-3" class="max-h-56 rounded-md py-1 text-base leading-6 shadow-xs overflow-auto focus:outline-none sm:text-sm sm:leading-5">
            @foreach($field->values() as $optionValue => $optionName)
                <li x-ref="item"
                    @click="selectOption();search='';open=!open;currentName = '{{ str_replace("'", '', $optionName) }}';currentId = '{{ $optionValue }}';currentImage = '{{ $field->searchableImageField() ? '' : '' }}';"
                    data-value="{{ $optionName }}"
                    role="option"
                    :class="currentId != {{$optionValue}} ? 'text-gray-900' : 'text-white bg-pink'"
                    class="cursor-default select-none relative py-2 pl-3 pr-9"
                >
                    <div class="flex items-center space-x-3">
                        <span :class="currentId != '{{$optionValue}}' ? 'font-normal' : 'font-semibold'"  class="block truncate">
                            {{ $optionName }}
                        </span>
                    </div>


                    <span :class="currentId != '{{$optionValue}}' ? 'hidden' : ''" class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            @endforeach
        </ul>
    </div>

    <script>
      function initMultiSelect_{{ $field->id() }}() {
        return {
          search: '',
          open: false,
          currentImage: '',
          @if(!$field->values())
          currentId: '',
          currentName: '',
          @else
          currentId: '{{ $field->formViewValue($item) ?? array_key_first($field->values()) }}',
          currentName: '{{ $field->formViewValue($item)
                            ? str_replace("'", '', $field->values()[$field->formViewValue($item)])
                            : str_replace("'", '', array_values($field->values())[0]) }}',
          @endif
          selectOption() {
            Array.prototype.slice.call(this.$refs.ul.getElementsByTagName("li")).forEach(function (li) {
              li.hidden = false;
            });
          },
          filterOptions() {
            var searchValue = this.search.toLowerCase();

            Array.prototype.slice.call(this.$refs.ul.getElementsByTagName("li")).forEach(function (li) {
              var value = li.getAttribute("data-value").toLowerCase();

              if(value.includes(searchValue)) {
                li.hidden = false;
              } else {
                li.hidden = true;
              }
            });
          }
        };
      }
    </script>
</div>