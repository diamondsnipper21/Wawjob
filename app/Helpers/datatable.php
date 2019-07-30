<?php

/**
 * @author KCG
 * @since July 03, 2017
 */
if ( !function_exists('render_admin_paginator_desc') ) {
    function render_admin_paginator_desc($pagination) {
        $start  = ($pagination->currentPage() - 1) * $pagination->perPage() + 1;
        $end    = min($pagination->total(), $pagination->currentPage() * $pagination->perPage());
        $totals = $pagination->total();

        if ($totals == 0)
            return "";

        return "Showing $start to $end of $totals entries";
    }
}
/**
 * @author KCG
 * @since July 03, 2017
 */
if ( !function_exists('render_admin_sort_class') ) {
    function render_admin_sort_class($sort, $sort_dir) {
        return ;
    }
}