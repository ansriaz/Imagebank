var photo_counter = 0;

Dropzone.options.myDropzone = { 

    // The configuration we've talked about above
    autoProcessQueue: false,
    uploadMultiple: false,
    parallelUploads: 1000,
    maxFilesize: 20,
    previewsContainer: '#dropzonePreview',
    previewTemplate: document.querySelector('#preview-template').innerHTML,
    addRemoveLinks: true,
    dictRemoveFile: 'Remove',
    dictFileTooBig: 'Image is bigger than 20MB',

    // The setting up of the dropzone
    init: function() {
        var context = this;

        // First change the button to actually tell Dropzone to process the queue.
        this.element.querySelector("button[type=submit]").addEventListener("click", function(e) {

            // console.log($("#datasetname").val());
            // console.log($("#selDataset :selected").text());
            if($("#datasetname").val() == "" && $("#selDataset :selected").text() == "Select Dataset")
            {
                alert("Kindly select dataset from the list or write new name for dataset.");
                e.preventDefault();
                return;
            }
            // console.log($("#classlabels").val());
            console.log($("#selClasses :selected").text());
            console.log($('#selClasses').find(":selected").text());
            if($("#classlabels").val() == "" && ($('#selClasses').find(":selected").text() == "" || $('#selClasses').find(":selected").text() == "Select Class"))
            {
                alert("Kindly select class from the list or write new name for dataset.");
            }
            // if($("#classlabels").val() == "" && $('#selClasses').find(":selected").text() == "Select Class")
            // {
            //     alert("Kindly select class from the list or write new name for dataset.");
            // }

            if (context.element.querySelector("input[type=checkbox]").checked) {

                // Make sure that the form isn't actually being sent.
                context.element.querySelector("div[id=error-terms]").style.display = 'none';
                e.stopPropagation();
                context.processQueue();
            } else {
                context.element.querySelector("div[id=error-terms]").style.display = 'block';
            };
            e.preventDefault();
            return;
        });

        // this.element.querySelector("input[type=checkbox]").click(function() {
        //     if (this.checked) {
        //         context.element.querySelector("div[id=error-terms]").style.display = 'none';
        //     } else {
        //         this.element.querySelector("input[type=checkbox]").checked = false;
        //     }
        // });

        this.on("removedfile", function(file) {

            $.ajax({
                type: 'POST',
                url: 'upload/delete',
                data: {id: file.name},
                dataType: 'html',
                success: function(data){
                    var rep = JSON.parse(data);
                    console.log(rep);
                    if(rep.code == 200)
                    {
                        photo_counter--;
                        $("#photoCounter").text( "(" + photo_counter + ")");
                    }

                },
                error: function(msg){
                }
            });
        } );

        // Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
        // of the sending event because uploadMultiple is set to true.
        this.on("sendingmultiple", function() {
          // Gets triggered when the form is actually being sent.
          // Hide the success button or the complete form.
        });
        this.on("successmultiple", function(files, response) {
            // Gets triggered when the files have successfully been sent.
            // Redirect user or notify of success.
        });
        this.on("errormultiple", function(files, response) {
            // Gets triggered when there was an error sending the files.
            // Maybe show form again, and notify user of error
            alert("error");
        });
        this.on("complete", function (file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                // window.location="{{ route('/dashboard') }}";
                alert('All files uploaded');
            }
        });
    },
    error: function(file, response) {
        if($.type(response) === "string")
            var message = response; //dropzone sends it's own error messages in string
        else
            var message = response.message;
        file.previewElement.classList.add("dz-error");
        _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i];
            _results.push(node.textContent = message);
        }
        return _results;
    },
    success: function(file,done) {
        photo_counter++;
        $("#photoCounter").text( "(" + photo_counter + ")");
    },
    // removedfile: function(file) {
    //     var name = file.name;
    //     $.ajax({
    //         type: 'POST',
    //         url: 'upload/delete',
    //         data: {id: file.name},
    //         dataType: 'html',
    //         success: function(data){
    //             var rep = JSON.parse(data);
    //             if(rep.code == 200)
    //             {
    //                 photo_counter--;
    //                 $("#photoCounter").text( "(" + photo_counter + ")");
    //             }
    //         },
    //         error: function(msg){
    //         }
    //     });
    // }
}




