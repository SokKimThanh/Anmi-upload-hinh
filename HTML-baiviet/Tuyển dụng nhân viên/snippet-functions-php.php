<?php
/**
 * Tắt wpautop (tự thêm <p> và <br>) cho các trang tuyển dụng
 * Dán đoạn này vào cuối file functions.php của theme WordPress
 * (thường ở: wp-content/themes/<tên-theme>/functions.php)
 *
 * ------------------------------------------------------------------
 * CÁCH 1: Tắt wpautop cho TOÀN BỘ nội dung bài viết (đơn giản nhất)
 * ------------------------------------------------------------------
 */
remove_filter( 'the_content', 'wpautop' );


/**
 * ------------------------------------------------------------------
 * CÁCH 2: Tắt wpautop chỉ cho post type "tuyen-dung" (an toàn hơn)
 * Thay 'tuyen-dung' bằng đúng post type slug bạn dùng trong WP
 * ------------------------------------------------------------------
 */
// add_action( 'wp', function () {
//     if ( is_singular( 'tuyen-dung' ) ) {
//         remove_filter( 'the_content', 'wpautop' );
//     }
// } );


/**
 * ------------------------------------------------------------------
 * CÁCH 3: Tắt wpautop cho một page cụ thể theo ID
 * Thay 123 bằng ID thực của page tuyển dụng trong WP
 * ------------------------------------------------------------------
 */
// add_action( 'wp', function () {
//     if ( is_page( array( 123, 456 ) ) ) {   // điền ID page vào đây
//         remove_filter( 'the_content', 'wpautop' );
//     }
// } );
