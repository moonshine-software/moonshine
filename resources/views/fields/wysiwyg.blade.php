<div class="hidden">
    @include("moonshine::fields.textarea", [
        'element' => $element,
        'item' => $item,
        'resource' => $resource,
    ])
</div>

<div>
    <trix-editor class="trix-editor" input="{{ $element->id() }}"></trix-editor>
</div>

<script>
    (function () {
        var HOST = "{{ route('moonshine.attachments') }}"

        addEventListener("trix-attachment-add", function (event) {
            if (event.attachment.file) {
                uploadFileAttachment(event.attachment)
            }
        })

        function uploadFileAttachment(attachment) {
            uploadFile(attachment.file, setProgress, setAttributes)

            function setProgress(progress) {
                attachment.setUploadProgress(progress)
            }

            function setAttributes(attributes) {
                attachment.setAttributes(attributes)
            }
        }

        function uploadFile(file, progressCallback, successCallback) {
            var key = createStorageKey(file)
            var formData = createFormData(key, file)
            var xhr = new XMLHttpRequest()

            xhr.open("POST", HOST, true)

            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

            xhr.upload.addEventListener("progress", function (event) {
                var progress = event.loaded / event.total * 100
                progressCallback(progress)
            })

            xhr.addEventListener("load", function (event) {
                if (xhr.status == 200) {
                    var response = JSON.parse(xhr.response);

                    var attributes = {
                        url: response.attachment,
                        href: response.attachment
                    }
                    successCallback(attributes)
                }
            })

            xhr.send(formData)
        }

        function createStorageKey(file) {
            var date = new Date()
            var day = date.toISOString().slice(0, 10)
            var name = date.getTime() + "-" + file.name
            return ["tmp", day, name].join("/")
        }

        function createFormData(key, file) {
            var data = new FormData()
            data.append("key", key)
            data.append("Content-Type", file.type)
            data.append("file", file)
            return data
        }
    })();
</script>

