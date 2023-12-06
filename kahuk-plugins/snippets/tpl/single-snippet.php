
<legend>Add new</legend>	

<ul>
    <li>If you use javascript in the code block, please wrap it with {literal} and {/literal} tags.</li>
    <li>For PHP code, wrap the code with {php} and {/php} tags.</li>
    <li>You can also use Smarty tags used in Kahuk templates.</li>
</ul>
	
<form action="" method="POST" id="snippetform">
    <table>
        <tbody>
            <tr>
                <td>
                    <label><strong>Name:</strong></label>
                </td>
                <td>
                    <input type="text" name="snippet_name" id="snippet_name" value="<?= stripslashes($single_snippet["snippet_name"]) ?>" class="form-control" />
                </td>
            </tr>
            <tr>
                <td>
                    <label><strong>Location:</strong></label>
                </td>
                <td>
                    <?php $snippets_locations = snippets_locations(); ?>
                    <select name="snippet_location" class="form-control">
                    <?php foreach($snippets_locations as $row) { ?>
                        <option value="<?= $row[0] ?>" <?php if ($row[0] == $single_snippet["snippet_location"]) { echo "selected='selected'"; } ?>><?= $row[0] ?> - <?= $row[1] ?></option>
                    <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label><strong>Status:</strong></label>
                </td>
                <td>
                    <select name="snippet_status" class="form-control">
                        <option value='1' <?php if ($single_snippet["snippet_status"] == 1) { echo "selected='selected'"; } ?>>Active</option>
                        <option value='0' <?php if ($single_snippet["snippet_status"] == 0) { echo "selected='selected'"; } ?>>Inactive</option>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    
    <label><strong>Content: </strong></label>
    <!-- snippet_content must have "|escape" modifier to prevent bug with textarea code in textarea -->
    <textarea id="textarea-1" name="snippet_content" rows="15" class="form-control"><?= stripslashes($single_snippet["snippet_content"]) ?></textarea>
    <br/>
    <div class="flex gap-4">
        <input type="submit" name="submit" value="Submit" class="btn btn-primary" />
        <a class="btn btn-back" href="<?= PLUGIN_SETTINGS_SNIPPETS ?>">Back</a>
    </div>
    
    <input type="hidden" name="snippet_id" value="<?= $single_snippet["snippet_id"] ?>" />
    <input type="hidden" name="action_type" value="submit-snippet" />
</form>

