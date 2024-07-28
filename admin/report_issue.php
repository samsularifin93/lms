<?php

//issue_book.php

include '../database_connection.php';

include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

$error = '';

include '../header.php';

?>
<div class="container-fluid py-4" style="min-height: 700px;">
	<h1>Report Tape</h1>
    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Report</li>
    </ol>
    <div class="row">
        <div class="col-md-6">
            <?php 
            if($error != '')
            {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="list-unstyled">'.$error.'</ul> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            ?>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-plus"></i> Report
                </div>
                <div class="card-body">
                    <form method="post" action="get_report_issue.php">
                        <div class="mb-3">
                            <label class="form-label">Date Range</label>
							<div class="input-daterange input-group" id="datepicker">
								<input type="date" class="input-sm form-control" name="start" id="start"/>
								<span class="input-group-addon" style="margin:0px 5px 0px 5px;"> - </span>
								<input type="date" class="input-sm form-control" name="end" id="end" />
							</div>
                        </div>
						<div class="mb-3">
                            <label class="form-label">Tape Number</label>
                            <input type="text" name="book_id" id="book_id" class="form-control" />
                            <span id="book_isbn_result"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User Unique ID</label>
							<select name="user_id" id="user_id" class="form-control">
        						<?php echo fill_user_id($connect); ?>
        					</select>
                            <span id="user_unique_id_result"></span>
                        </div>
						<div class="mb-3">
                            <label class="form-label">Status</label>
							<select name="status" id="status" class="form-control">
        						<option value="">All</option>
								<option value="Return">Return</option>
								<option value="Issue">Issue</option>
        					</select>
                            <span id="user_unique_id_result"></span>
                        </div>
                        <div class="mt-4 mb-0">
                            <input type="submit" name="issue_book_button" class="btn btn-success" value="Export (.xls)" />
                        </div>  
                    </form>
                    <script>
                    var book_id = document.getElementById('book_id');

                    book_id.onkeyup = function()
                    {
                        if(this.value.length > 2)
                        {
                            var form_data = new FormData();

                            form_data.append('action', 'search_book_isbn');

                            form_data.append('request', this.value);

                            fetch('action.php', {
                                method:"POST",
                                body:form_data
                            }).then(function(response){
                                return response.json();
                            }).then(function(responseData){
                                var html = '<div class="list-group" style="position:absolute; width:93%">';

                                if(responseData.length > 0)
                                {
                                    for(var count = 0; count < responseData.length; count++)
                                    {
                                        html += '<a href="#" class="list-group-item list-group-item-action"><span onclick="get_text(this)">'+responseData[count].isbn_no+'</span> - <span class="text-muted">'+responseData[count].book_name+'</span></a>';
                                    }
                                }
                                else
                                {
                                    html += '<a href="#" class="list-group-item list-group-item-action">No Tape Found</a>';
                                }

                                html += '</div>';

                                document.getElementById('book_isbn_result').innerHTML = html;
                            });
                        }
                        else
                        {
                            document.getElementById('book_isbn_result').innerHTML = '';
                        }
                    }

                    function get_text(event)
                    {
                        document.getElementById('book_isbn_result').innerHTML = '';

                        document.getElementById('book_id').value = event.textContent;
                    }

                    var user_id = document.getElementById('user_id');

                    user_id.onkeyup = function(){
                        if(this.value.length > 2)
                        {   
                            var form_data = new FormData();

                            form_data.append('action', 'search_user_id');

                            form_data.append('request', this.value);

                            fetch('action.php', {
                                method:"POST",
                                body:form_data
                            }).then(function(response){
                                return response.json();
                            }).then(function(responseData){
                                var html = '<div class="list-group" style="position:absolute;width:93%">';

                                if(responseData.length > 0)
                                {
                                    for(var count = 0; count < responseData.length; count++)
                                    {
                                        html += '<a href="#" class="list-group-item list-group-item-action"><span onclick="get_text1(this)">'+responseData[count].user_unique_id+'</span> - <span class="text-muted">'+responseData[count].user_name+'</span></a>';
                                    }
                                }
                                else
                                {
                                    html += '<a href="#" class="list-group-item list-group-item-action">No User Found</a>';
                                }
                                html += '</div>';

                                document.getElementById('user_unique_id_result').innerHTML = html;
                            });
                        }
                        else
                        {
                            document.getElementById('user_unique_id_result').innerHTML = '';
                        }
                    }

                    function get_text1(event)
                    {
                        document.getElementById('user_unique_id_result').innerHTML = '';

                        document.getElementById('user_id').value = event.textContent;
                    }

                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 

include '../footer.php';

?>