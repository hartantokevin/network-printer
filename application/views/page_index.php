<head>
    <title>SSE</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <style>
        .add {
            margin-top: 10px;
            margin-left: auto;
            margin-right: 10px;
            position: relative;
            float: right;
        }
    </style>
</head>
<div class="card" id="today">
    <div class="col-md-12">
        <button id="close">Close the connection</button>
        <button id="open">Refresh the connection</button>
    </div>
    <div class="card-body">
        <div class="table-responsive" id="table-content">
            <table id="data-table" class="table table-hover">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>job</th>
                        <th>status</th>
                        <th>created by</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody id="job_list">
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function update(id) {
        $.ajax({
            url: '<?= base_url() ?>index.php/sse/update/' + id,
            type: 'POST',
            data: $('#' + id).serialize(),
            dataType: 'JSON',
            success: function(res) {
                $('#mdlUpdate' + id).modal('hide')
                console.log(res)
            },
            error: function(e) {
                $('#mdlUpdate' + id).modal('hide')
                console.log(e);
            }
        })
    }

    $('#job_list').on('click', 'span', function(e) {
        e.preventDefault()
        let id = $(this).data("id")
        update(id)
    })

    function tampil() {
        $.ajax({
            url: '<?= base_url() ?>index.php/sse/detail',
            type: 'POST',
            dataType: 'JSON',
            success: function(res) {
                let html
                for (data of res) {
                    html += `<tr>`
                    html += `<td>` + data.id + `</td>`
                    html += `<td>` + data.job + `</td>`
                    html += `<td>` + data.status + `</td>`
                    html += `<td>` + data.created_by + `</td>`
                    html += `<td>`
                    html += `<button type="button" class="btn btn-warning btn-xs" data-bs-toggle="modal" data-bs-target="#mdlUpdate` + data.id + `">
                                Edit
                            </button>`
                    // MODAL
                    html +=
                        `<div class="modal fade" id="mdlUpdate` + data.id + `" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                                <div class=" modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Data</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form id=` + data.id + `>
                                        <div class="modal-body">
                                            <input type="hidden" value="` + data.id + `" name="id">
                                            <div class="form-group">
                                                <label>Job</label>
                                                <input type="text" class="form-control" value="` + data.job + `" name="job">
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <input type="text" class="form-control" value="` + data.status + `" name="status">
                                            </div>
                                            <div class="form-group">
                                                <label>Created By</label>
                                                <input type="text" class="form-control" value="` + data.created_by + `" name="created_by">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <span id="update" data-id="` + data.id + `" class="btn btn-primary">Save Changes</span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>`
                    html += ` </td>`
                    html += `</tr>`


                }
                $('#job_list').html(html)

                evtSrc()
            },
            error: function(e) {
                console.log(e)
            }
        })
    }

    function evtSrc() {
        let closeButton = document.getElementById('close')
        let openButton = document.getElementById('open')
        let evtSource = new EventSource('<?= base_url() ?>index.php/sse/event_data')

        evtSource.onopen = function() {
            openButton.disabled = true
            closeButton.disabled = false
            console.log("Connection to server opened.")
        };

        evtSource.onmessage = function(e) {
            let html
            let eJSON = JSON.parse(e.data)
            for (data of eJSON) {
                html += `<tr>`
                html += `<td>` + data.id + `</td>`
                html += `<td>` + data.job + `</td>`
                html += `<td>` + data.status + `</td>`
                html += `<td>` + data.created_by + `</td>`
                html += `<td>`
                html += `<button type="button" class="btn btn-warning btn-xs" data-bs-toggle="modal" data-bs-target="#mdlUpdate` + data.id + `">
                                Edit
                            </button>`
                // MODAL
                html +=
                    `<div class="modal fade" id="mdlUpdate` + data.id + `" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class=" modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id=` + data.id + `>
                                    <div class="modal-body">
                                        <input type="hidden" value="` + data.id + `" name="id">
                                        <div class="form-group">
                                            <label>Job</label>
                                            <input type="text" class="form-control" value="` + data.job + `" name="job">
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label>
                                            <input type="text" class="form-control" value="` + data.status + `" name="status">
                                        </div>
                                        <div class="form-group">
                                            <label>Created By</label>
                                            <input type="text" class="form-control" value="` + data.created_by + `" name="created_by">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <span id="update" data-id="` + data.id + `" class="btn btn-primary">Save Changes</span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>`
                html += ` </td>`
                html += `</tr>`
            }
            $('#job_list').html(html)
            console.log("refreshed")
        }

        evtSource.onerror = function() {
            console.log("EventSource failed.")
        }

        closeButton.onclick = function() {
            evtSource.close()
            openButton.disabled = false
            closeButton.disabled = true
            console.log('Connection closed')
        }

        openButton.onclick = function() {
            location.reload()
            openButton.disabled = true
        }

        evtSource.addEventListener("ping", function(e) {
            var obj = JSON.parse(e.data)
            // console.log(obj.length)
        }, false)

        // if close tab, evt src will be shut down
        $(window).on('beforeunload', function() {
            evtSource.close();
        })
    }

    tampil()
</script>