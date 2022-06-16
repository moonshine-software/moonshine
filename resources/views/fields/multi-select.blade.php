<div x-data="multiSelect_{{ $field->id() }}()" x-init="loadOptions()" class="w-full flex flex-col items-center mx-auto">
    <select
            x-model="{{ $field->isMultiple() ? 'selectValues' : 'selectValue' }}"
            x-ref="multi_select_{{ $field->id() }}"
            {!! $field->meta() ?? '' !!}
            id="{{ $field->id() }}"
            name="{{ $field->name() }}"
            {{ $field->isRequired() ? "required" : "" }}
            {{ $field->isMultiple() ? "multiple" : "" }}
            style="display: none;"
    >
        @if(!$field->isMultiple() && $field->isNullable())
            <option @selected(!$field->formViewValue($item)) value="">-</option>
        @endif

        @foreach($field->values() as $optionValue => $optionName)
            <option @selected($field->isSelected($item, $optionValue)) value="{{ $optionValue }}">
                {{ $optionName }}
            </option>
        @endforeach
    </select>

    <div class="inline-block relative w-full">
        <div class="flex flex-col items-center relative">
            <div x-on:click="open" class="w-full">
                <div class="p-1 flex items-center justify-between border border-gray-200 bg-white dark:bg-darkblue rounded">
                    <div class="flex flex-auto flex-wrap">
                        <template x-for="(option,index) in selectedOptions()" :key="index">
                            <div class="flex justify-center items-center m-2 p-2 font-medium bg-purple rounded text-white border">
                                <div class="text-xs font-normal leading-none max-w-full flex-initial" x-text="option.text"></div>
                                <div class="flex flex-auto flex-row-reverse">
                                    @if($field->isMultiple())
                                        <div x-on:click.stop="remove(index)">
                                            <svg class="fill-current h-4 w-4 " viewBox="0 0 20 20">
                                                <path d="M14.348,14.849c-0.469,0.469-1.229,0.469-1.697,0L10,11.819l-2.651,3.029c-0.469,0.469-1.229,0.469-1.697,0
                                               c-0.469-0.469-0.469-1.229,0-1.697l2.758-3.15L5.651,6.849c-0.469-0.469-0.469-1.228,0-1.697s1.228-0.469,1.697,0L10,8.183
                                               l2.651-3.031c0.469-0.469,1.228-0.469,1.697,0s0.469,1.229,0,1.697l-2.758,3.152l2.758,3.15
                                               C14.817,13.62,14.817,14.38,14.348,14.849z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="text-gray-300 dark:text-white w-8 py-1 pl-2 pr-1 border-l flex items-center border-gray-200">
                        <button type="button" x-show="isOpen() === true" x-on:click="open" class="cursor-pointer w-6 h-6 outline-none focus:outline-none">
                            <svg version="1.1" class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                <path d="M17.418,6.109c0.272-0.268,0.709-0.268,0.979,0s0.271,0.701,0,0.969l-7.908,7.83
                                    c-0.27,0.268-0.707,0.268-0.979,0l-7.908-7.83c-0.27-0.268-0.27-0.701,0-0.969c0.271-0.268,0.709-0.268,0.979,0L10,13.25
                                    L17.418,6.109z" />
                            </svg>
                        </button>

                        <button type="button" x-show="isOpen() === false" @click="close" class="cursor-pointer w-6 h-6 outline-none focus:outline-none">
                            <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                <path d="M2.582,13.891c-0.272,0.268-0.709,0.268-0.979,0s-0.271-0.701,0-0.969l7.908-7.83
	c0.27-0.268,0.707-0.268,0.979,0l7.908,7.83c0.27,0.268,0.27,0.701,0,0.969c-0.271,0.268-0.709,0.268-0.978,0L10,6.75L2.582,13.891z
	" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-full px-4">
                <div x-show.transition.origin.top="isOpen()" class="absolute shadow-lg top-100 bg-white dark:bg-darkblue z-40 w-full left-0 rounded max-h-select" x-on:click.outside="close">
                    <input placeholder="Поиск"
                           type="text"
                           x-on:keydown="filterOptions()"
                           x-model="search"
                           value=""
                           class="text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline rounded-lg py-2 px-4 block w-full appearance-none leading-normal"
                    />

                    <div class="flex flex-col w-full overflow-y-auto h-64">
                        <template x-for="(option,index) in options" :key="index" class="overflow-auto">
                            <div x-show="!option.hidden" class="cursor-pointer w-full border-gray-100 rounded-t border-b" @click="select(index)">
                                <div class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                                    <div class="w-full items-center flex justify-between">
                                        <div class="mx-2 leading-6 text-black dark:text-white" x-model="option" x-text="option.text"></div>
                                        <div x-show="option.selected">
                                            <svg class="text-purple w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  function multiSelect_{{ $field->id() }}() {
    return {
      search: '',
      options: [],
      selectValues: [],
      selectValue: '',
      show: false,
      open() { this.show = true },
      close() { this.show = false },
      isOpen() { return this.show === true },
      select(index) {
          @if(!$field->isMultiple())
              this.options = this.options.map(function(option, i) {
            if(index === i) {
              option.selected = true;
            } else {
              option.selected = false;
            }

            return option;
          });

        this.close()
          @else
          if (!this.options[index].selected) {
            this.add(index);
          } else {
            this.remove(index);
          }
          @endif
      },
      add(index) {
        this.options[index].selected = true;
      },
      remove(index) {
        this.options[index].selected = false;
      },
      loadOptions() {
        const options = this.$refs.multi_select_{{ $field->id() }}.options;

        for (let i = 0; i < options.length; i++) {
          this.options.push({
            value: options[i].value,
            text: options[i].innerText,
            selected: options[i].hasAttribute('selected'),
            hidden: false,
          });
        }
      },
      selectedOptions(){
        const selected = this.options.filter((option) => option.selected);
        this.selectValues = Object.keys(selected).map((key) => [selected[key].value]);
        this.selectValue = selected.length ? selected[0].value : ''
        return selected;
      },
      filterOptions() {
        const searchValue = this.search.toLowerCase();

        for (let i = 0; i < this.options.length; i++) {
          const optionText = this.options[i].text.toLowerCase();

          this.options[i].hidden = !optionText.includes(searchValue);
        }
      }
    }
  }
</script>