<p>
    Quickly insert blocks of code into your templates. Just create a new snippet by giving it a name, a location where it should appear and the code that you want to insert in that spot.
</p>

<h3>Instructions</h3>
<p>
    <strong><em>When adding Google Analytics or Adsense code, their entire code MUST be on one line with no line breaks at all! See the illustration below. (View the picture)</em></strong>
    <br>
    <img src="<?= kahuk_create_plugin_url(PLUGIN_SLUG_SNIPPETS . "/assets/adsense-code-snippet-pluggin.png"); ?>" style="width:100%;hight:auto" alt="Proper Google Analytics and Adsense code formatting" title="Proper Google Analytics and Adsense code formatting">
</p>

<form name="snippet" method="post" enctype='multipart/form-data'>
    <table class="table table-bordered table-striped">
		<thead>
			<tr>
                <th style="width:50px;">
                    <input type="checkbox"/>
                </th>	
				<th>Name</th>
				<th>Location</th>
				<th>Updated</th>
				<th style="width:75px;">Order</th>
				<th style="width:75px;">Status</th>
			</tr>
        </thead>
		<tbody>
            <?php foreach($snippetsObj->snippetsById as $i => $row) { ?>
                <tr>
                    <td>
                        <input type="checkbox" name="snippet_checkbox[<?= $row["snippet_id"] ?>" id="snippet-checkbox-[<?= $row["snippet_id"] ?>" value="1"/>
                    </td>
                    <td>
                        <?php $url = kahuk_create_url("admin/admin_plugin.php?plugin=" . PLUGIN_SLUG_SNIPPETS . "&page=single-snippet&action=" . $row["snippet_id"]); ?>
                        <a href="<?= $url ?>">
                            <?php echo $row["snippet_name"]; ?>
                        </a>
                    </td>
                    <td><?= $row["snippet_location"] ?></td>
                    <td><?= $row["snippet_updated"] ?></td>
                    <td>
                        <input type="text" name="snippet_order[<?= $row["snippet_id"] ?>]" id="order-<?= $row["snippet_id"] ?>" value="<?= $row["snippet_order"] ?>" class="form-control">
                    </td>
                    <td>
                        <input type="text" name="snippet_status[<?= $row["snippet_id"] ?>]" id="status-<?= $row["snippet_id"] ?>" value="<?= $row["snippet_status"] ?>" class="form-control">
                    </td>
                </tr>	
            <?php } ?>
        </tbody>
    </table>

    <div class="flex gap-4">
        <!-- <input type="submit" name="submit" value="Update" class="btn btn-primary" /> -->
        <a class="btn btn-primary" href="<?= $single_page_url . "new" ?>">New</a>
    </div>
</form>
