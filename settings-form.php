<h1>Advanced JS Defer Settings</h1>

<?php
    if (isset($_POST['submit'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Settings have been saved! Please clear cache if you\'re using a cache plugin</p></div>';
    }
?>

<form method="POST">
    <?php wp_nonce_field( 'advanced-js-defer', 'advanced-js-defer-settings-form' ); ?>
    <table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row"><label>JavaScript execution delay</label></th>
            <td>
                <select name="delay" value="<?php echo $delay; ?>">
                    <option value="0" <?php if ($delay == 0) echo 'selected'; ?>>0 seconds</option>
                    <option value="1" <?php if ($delay == 1) echo 'selected'; ?>>1 seconds</option>
                    <option value="2" <?php if ($delay == 2) echo 'selected'; ?>>2 seconds</option>
                    <option value="3" <?php if ($delay == 3) echo 'selected'; ?>>3 seconds</option>
                    <option value="4" <?php if ($delay == 4) echo 'selected'; ?>>4 seconds</option>
                    <option value="5" <?php if ($delay == 5) echo 'selected'; ?>>5 seconds</option>
                    <option value="10" <?php if ($delay == 10) echo 'selected'; ?>>10 seconds</option>
                </select>
                <p class="description">Delay to start JavaScript execution after receiving <code>requestIdleCallback</code></p>
            <td>
        </tr>
    </tbody>
    </table>
    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
</form>