<?php
header("Content-Type:text/css");
$color = "";
$color2 = "";
$color3 = "";
function checkhexcolor($c)
{
    return preg_match('/^[a-f0-9]{6}$/i', $c);
}
if (isset($_GET['color']) && !empty($_GET['color']) && checkhexcolor($_GET['color'])) {
    $color = '#' . $_GET['color'];
}
if (!$color) {
    $color = "#ec4e20";
}
if (isset($_GET['color2']) && !empty($_GET['color2']) && checkhexcolor($_GET['color2'])) {
    $color2 = '#' . $_GET['color2'];
}
if (!$color2) {
    $color2 = "#faa603";
}
function hex2rgba($color, $opacity)
{
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }
    if (strlen($color) == 6) {
        list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return false;
    }
    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);
    $rgb = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $opacity . ')';
    return $rgb;
}

?>
.custom-button:hover {
color: #ffffff;
}
h1 a:hover,
h2 a:hover,
h3 a:hover,
h4 a:hover,
h5 a:hover,
h6 a:hover {
color: <?php echo $color ?>;
}

.header-bottom {
background: #0f1932;
}

@media (max-width: 991px){
.menu {
background: #0f1932;
}

.menu li:hover > a {
background: #0f1932;
color: #ffffff;
}
.menu li .submenu li a {
color: #ffffff;
}

}

.menu li a {
color: #ffffff;
}
.header-bottom.active {
background: #0f1932;
border-bottom: 1px solid <?php echo $color ?>;
}

a.Attachment:hover {
color: <?php echo $color ?>;
}

.header-bar span{
background-color: #ffffff;
}


*::selection {
background-color: <?php echo $color ?>;
color: #17264d;
}
.social-icons li a:hover {
color: #ffffff;
background: <?php echo $color ?>;
}
.header-top {
background: <?php echo $color ?>;
}
.menu li a:hover {
color: <?php echo $color ?>;
}
.menu li a.header-button {
background: <?php echo $color ?> !important;
}
.menu li .header-button {
background: <?php echo $color ?>;
}
.widget.widget-post ul li a:hover .subtitle{
color: <?php echo $color ?>;
}
.menu li .submenu li a.active {
background: <?php echo $color ?>;
}
.menu li .submenu li:hover > a {
background: <?php echo $color ?>;
}
h3.sub-title {
color: <?php echo $color ?>;
}
.banner-prev,
.banner-next {
color: <?php echo $color ?>;
}
.client-item blockquote::before {
color: <?php echo $color ?>;
}
.feature-item .feature-header .icon {
color: <?php echo $color ?>;
}
.feature-item:hover, .feature-item.active {
background: <?php echo $color ?>;
}
.section-bg .how-item:hover {
background: <?php echo $color ?>;
}
.how-item .how-thumb {
color: <?php echo $color ?>;
}
.how-item:hover, .how-item.active {
background: <?php echo $color ?>;
color: #ffffff;
}
.how-item::before {
color: <?php echo $color ?>;
}
.video-wrapper .video-button {
background: <?php echo $color ?>;
}
.video-wrapper .video-button::after, .video-wrapper .video-button::before {
background: <?php echo $color ?>;
}
.common-list li::before {
color: <?php echo $color ?>;
}
.subscribe-form button {
color: <?php echo $color ?>;
}
.section-bg .ticket-item:hover {
background: <?php echo $color ?>;
}
.ticket-item .ticket-body ul li .icon {
color: <?php echo $color ?>;
}
.ticket-item:hover, .ticket-item.active {
background: <?php echo $color ?>;
}
.breadcrumb li a:hover {
color: <?php echo $color ?>;
}
.common-form-style p.terms-and-conditions a {
color: <?php echo $color ?>;
}
.create-account-form .form-group input[type="submit"] {
background: <?php echo $color ?>;
}
.create-account-form .form-group input[type="submit"]:hover {
background: transparent;
color: <?php echo $color ?>;
border-color: <?php echo $color ?>;
}
.create-account-form .form-group .checkbox-wrapper .checkbox-item input[type="checkbox"]:checked::before {
background: <?php echo $color ?>;
}
.change-catagory-area::after {
background: <?php echo $color ?>b8;
}
.change-catagory-area .custom-button:hover {
background: <?php echo $color ?>;
border-color: #ffffff;
}
.sign-up-option li a {
background: <?php echo $color ?>;
}
.faq-item.open .faq-title .title {
color: <?php echo $color ?>;
}
.faq-item.open .faq-title .right-icon::after, .faq-item.open .faq-title .right-icon::before {
background: <?php echo $color ?>;
}
.contact-form .form-group input:focus {
border-color: <?php echo $color ?>;
}
.contact-form .form-group input[type="submit"] {
background: <?php echo $color ?>;
}
.contact-form .form-group textarea:focus {
border-color: <?php echo $color ?>;
}
.contact--item .contact-content ul li a:hover {
color: <?php echo $color ?>;
}
.contact--item .contact-thumb {
color: <?php echo $color ?>;
}
.contact--item::before, .contact--item::after {
background: <?php echo $color ?>;
}
.shop-item .shop-thumb .shop-thumb-content li a {
color: <?php echo $color ?>;
}
.shop-item:hover .shop-content .title a {
color: <?php echo $color ?>;
}
.shop-area .shop-area-content .cart-button button {
background: <?php echo $color ?>;
}
.review .review-menu li.active {
background-color: <?php echo $color ?>;
}
.review .review-content .review-showing .client-review .review-form .client-form button {
background-color: <?php echo $color ?>;
}
.modal .modal-dialog .modal-content .modal-body .product-details-inner .product-content p i {
color: <?php echo $color ?>;
}
.cart-button button {
background-color: <?php echo $color ?>;
}
.shop-cart .section-wrapper .cart-top table thead tr th {
background: <?php echo $color ?>;
}
.shop-cart .section-wrapper .cart-top table tbody tr td.product-item .p-content a:hover {
color: <?php echo $color ?>;
}
.shop-cart .section-wrapper .cart-bottom .cart-checkout-box .coupon input[type="submit"] {
background: <?php echo $color ?>;
border-color: <?php echo $color ?>;
}
.shop-cart .section-wrapper .cart-bottom .shiping-box .calculate-shiping .outline-select .select-icon {
background: <?php echo $color ?>;
border: 1px solid <?php echo $color ?>;
}
.shop-cart .section-wrapper .cart-bottom .shiping-box .calculate-shiping button {
background: <?php echo $color ?>;
border-color: <?php echo $color ?>;
}
.shop-cart .section-wrapper .cart-bottom .shiping-box .cart-overview ul li .pull-right {
margin-bottom: 0;
color: <?php echo $color ?>;
}
.counter-item .counter-header .title {
color: <?php echo $color ?>;
}
.post-item .post-content .meta-post li a i {
color: <?php echo $color ?>;
}
.post-item:hover .post-content .title a {
color: <?php echo $color ?>;
}
.post-item:hover .post-content a:hover {
color: <?php echo $color ?>;
}
.comment-form .form-group input[type="submit"] {
background: <?php echo $color ?>;
}
.scrollToTop {
color: <?php echo $color ?>;
}
.scrollToTop:hover {
color: <?php echo $color ?>;
}
@keyframes neon {
0%, 100% {
text-shadow: 4px 0 4px <?php echo $color ?>;
}
50% {
text-shadow: 6px 0 14px <?php echo $color ?>;
}
}
@-webkit-keyframes neon {
0%, 100% {
text-shadow: 4px 0 4px <?php echo $color ?>;
}
50% {
text-shadow: 6px 0 14px <?php echo $color ?>;
}
}
@-moz-keyframes neon {
0%, 100% {
text-shadow: 4px 0 4px <?php echo $color ?>;
}
50% {
text-shadow: 6px 0 14px <?php echo $color ?>;
}
}
.custom-button:hover {
background: <?php echo $color ?>;
border-color: <?php echo $color ?>;
}
.custom-button.theme {
color: #17264d;
background: <?php echo $color ?>;
border-color: <?php echo $color ?>;
}
#overlayer {
background: <?php echo $color ?>;
}
.deposit-tab .tab-menu .custom-button.active {
background: <?php echo $color ?>;
border-color: <?php echo $color ?>;
}
.deposite-table table thead tr th {
background: <?php echo $color ?>;
}

a.btn--base,
.btn--base{
background: <?php echo $color ?>!important;
border-color: <?php echo $color ?>!important;
color:#fff
}

.btn--base:hover{
color:#fff;
opacity: 0.9
}

.btn:focus{
box-shadow: none!important
}

.bg--base{
background: <?php echo $color ?>!important;
}

.text--base{
color: <?php echo $color ?>!important;
}

.border--base{
border-color: <?php echo $color ?>!important;
}

.custom-button.disabled:hover {
background-color: <?php echo $color ?>;
}
.menu-item-has-children.open > a::after {
color: <?php echo $color ?> !important;
}
@media(max-width: 991px) {
.menu-item-has-children.open > a {
color: <?php echo $color ?> !important;
}
}

.create-account-form .form-group input:focus,.form-control:focus {
border-color: <?php echo $color ?>;
}

.form--check .form-check-input:checked {
background-color: <?php echo $color ?> !important;
border-color: <?php echo $color ?> !important;
}
.form--check .form-check-input {
border: 1px solid <?php echo $color ?> !important;
}
.pagination .page-item .page-link {
color: <?php echo $color ?> ;
}
.pagination .page-item.active .page-link, .pagination .page-item .page-link:hover {
background-color: <?php echo $color ?>;
border-color: <?php echo $color ?>;
}

.feature-item::before {
color: <?php echo $color ?>;
}