export function getInputs() {
    const inputs = {}
    document.querySelectorAll('#moonshine-form [name]').forEach(element => {

        let value

        const type = element.getAttribute('type')

        if (element.hasAttribute('multiple') && element.options !== undefined) {
            value = []
            for (let option of element.options) {
                if (option.selected) {
                    value.push(option.value);
                }
            }
        } else if(type === 'checkbox' || type === 'radio') {
            value = element.checked
        } else {
            value = element.value
        }

        inputs[inputFieldName(element.getAttribute('name'))] = value
    })

    return inputs
}

export function showWhenChange(fieldName) {

    fieldName = inputFieldName(fieldName);

    this.whenFields.forEach(field => {
        if (fieldName != field.changeField) {
            return
        }
        this.showWhenVisibilityChange(fieldName, this.getInputs(), field)
    })
}

export function showWhenVisibilityChange(fieldName, inputs, field) {
    if (inputs[field.showField] === undefined) {
        return
    }

    const inputElement = document.querySelector('#moonshine-form [name=' + field.showField + ']')
    const fieldContainer = inputElement.closest('.form-group')

    let validateShow = false;

    switch (field.operator) {
        case '=':
            validateShow = inputs[field.changeField] == field.value
            break;
        case '!=':
            validateShow = inputs[field.changeField] != field.value
            break;
        case '>':
            validateShow = inputs[field.changeField] > field.value
            break;
        case '<':
            validateShow = inputs[field.changeField] < field.value
            break;
        case '>=':
            validateShow = inputs[field.changeField] >= field.value
            break;
        case '<=':
            validateShow = inputs[field.changeField] <= field.value
            break;
        case 'in':
            validateShow = field.value.indexOf(inputs[field.changeField]) !== -1
            break;
        case 'not in':
            validateShow = field.value.indexOf(inputs[field.changeField]) === -1
            break;
    }

    if (validateShow) {
        fieldContainer.style.removeProperty('display')
    } else {
        fieldContainer.style.display = 'none'
    }
}

function inputFieldName(inputName)
{
    inputName = inputName.replace('[]', '')
    if(inputName.indexOf('slide[') !== -1) {
        inputName = inputName.replace('slide[','').replace(']','')
    }
    return inputName;
}