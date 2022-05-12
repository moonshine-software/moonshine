@include("moonshine::fields.input", [
    "field" => $field,
    "resource" => $resource,
    "item" => $resource->getModel()
])