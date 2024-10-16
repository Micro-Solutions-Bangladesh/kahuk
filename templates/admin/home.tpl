<h2>Hello</h2>
<p>Welcome to dashboard</p>

<div class="page-dashboard flex flex-wrap gap-6">
    <div class="section">
        <h4>Want to Optimize Database?</h4>
        <div class="content">
            <form name="todo_a_good_name" id="todo_form_id_1" 
                action="{$kahuk_base_url}/admin/admin_index.php" method="post">

                <input type="submit" name="submit" value="Yes Optimize" class="btn btn-primary">
            </form>

            {$msg_optimize_database}
        </div>
    </div>

    <div class="section">
        <h4>Want to Delete All Trash Stories?</h4>
        <div class="content">
            <form name="todo_a_good_name" id="todo_form_id_2" 
                action="{$kahuk_base_url}/admin/admin_index.php" method="post">

                <input type="submit" name="submit" value="Yes Delete" class="btn btn-primary">
            </form>

            {$msg_delete_trash_stories}
        </div>
    </div>

    <div class="section">
        <h4>Test/Send Email</h4>
        <div class="content">
            <form name="todo_a_good_name" id="todo_form_id_3" 
                action="{$kahuk_base_url}/admin/admin_index.php" method="post">

                <div class="form-group">
                    <label for="sendType">Send Type</label>
                    <select name="send_type" class="form-control" id="sendType" required>
                        <option value="">Select Option</option>
                        <option value="smtp">SMTP</option>
                        <option value="mail">System Mail</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="emailSendReason">Email Send Reason</label>
                    <select name="email_send_reason" class="form-control" id="emailSendReason" required>
                        <option value="">Select Option</option>
                        <option value="1">Test Email</option>
                        <option value="2">Send Real Email</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="emailReceiver">Email Receiver</label>
                    <input name="email_receiver" type="email" class="form-control" id="emailReceiver" placeholder="name@example.com" required>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input name="subject" type="text" class="form-control" id="subject" placeholder="Testing email send" required>
                </div>

                <div class="form-group">
                    <label for="emailMassage">Massage</label>
                    <textarea name="email_massage" class="form-control" id="emailMassage" rows="8" required></textarea>
                </div>

                <input type="submit" name="submit" value="Send Email" class="btn btn-primary">
            </form>

            {$msg_test_or_send_email}
        </div>
    </div>
</div>
