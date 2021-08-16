$("#chooseFile").submit(function( event ) {
    event.preventDefault();
    var formData = new FormData();
    formData.append('file', $('#file')[0].files[0]);
    $.ajax({
        type: "POST",
        url: '/image/upload',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response)
        {
            if (response.error) {
                alert(response.error);
            } else {
                alert(response);
            }
        },
        error: function()
        {
            alert('Something went wrong! ');
        },
        complete: function()
        {
            /**
             * Prevent browser error net::ERR_UPLOAD_FILE_CHANGED
             * after subsequently re-uploading same file, but with updated data;
             */
            $('#file').val(null);
        }
    });
});