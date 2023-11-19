<?php
/**
 * Create an array of CSS classes for the body tag
 * 
 * @since 5.0.7
 * 
 * @return array
 */
function kahuk_get_body_css_classes($classes = "") {
    global $globalURL, $upname, $hooks;

    $output = [];
    $output[] = $upname . "-page";

    if ($globalURL->controller !== $upname) {
        $output[] = $globalURL->controller . "-page";
    }

    if (!empty($classes)) {
        if (is_array($classes)) {
            $classes = explode(" ", $classes);
        }

        $output = array_merge($output, $classes);
    }

    $output = $hooks->apply_filters('body_css_classes', $output);

    return $output;
}

/**
 * Print the CSS classes for the body tag
 * 
 * @since 5.0.7
 * 
 * @return void
 */
function kahuk_body_css_classes($classes = "") {
    $output = kahuk_get_body_css_classes($classes);
    echo implode(" ", $output);
}

/**
 * 
 */
function kahuk_select_options_markup($data, $selected = '') {
    $output = "";

    foreach($data as $v) {
        if ($selected == $v) {
            $output .= "<option value=\"{$v}\" selected=\"selected\">{$v}</option>";
        } else {
            $output .= "<option value=\"{$v}\">{$v}</option>";
        }
    }

    return $output;
}

/**
 * 
 */
function kahuk_statuses_select_markup($statuses, $attr='', $first_opt='', $selected = '') {
    global $hooks;

    $initData = [];

    foreach($statuses as $v) {
        $initData[$v] = $v;
    }

    $data = $hooks->apply_filters("statuses_array", $initData);

    $options = kahuk_select_options_markup($data, $selected);

    return "<select {$attr}>{$first_opt}{$options}</select>";
}

/**
 * 
 */
function kahuk_page_size_options_markup($attr='', $first_opt='', $selected = '') {
    global $hooks;

    $initData = [
        "25" => 25,
        "50" => 50,
        "100" => 100,
        "150" => 150,
        "200" => 200,
        "500" => 500,
    ];

    $data = $hooks->apply_filters("page_size_options_array", $initData);

    $options = kahuk_select_options_markup($data, $selected);

    return "<select {$attr}>{$first_opt}{$options}</select>";
}
