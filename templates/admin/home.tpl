<h2>Hello</h2>
<p>Welcome to dashboard</p>

<div class="row">
    <div class="section col-12 col-md-6">
        <h4>Want to Optimize Database?</h4>
        <div class="content">
            <form name="todo_a_good_name" id="todo_a_good_id" 
                action="{$kahuk_base_url}/admin/admin_index.php" method="post">

                <input type="submit" name="submit" value="Yes Optimize" class="btn btn-primary">
            </form>

            {$msg_optimize_database}
        </div>
    </div>
    <div class="section col-12 col-md-6">
        <h4>Want to Delete All Trash Stories?</h4>
        <div class="content">
            <form name="todo_a_good_name" id="todo_a_good_id" 
                action="{$kahuk_base_url}/admin/admin_index.php" method="post">

                <input type="submit" name="submit" value="Yes Delete" class="btn btn-primary">
            </form>

            {$msg_delete_trash_stories}
        </div>
    </div>
</div>
