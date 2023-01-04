<?php
/**
 * Modal template
 *
 * @since 1.1.0
 * @package Sellkit popup.
 */

?>

<style>
.sellkit-popup-box {
	position: absolute;
	width: 100%;
	height: 100%;
	top: 0;
	bottom: 0;
	right: 0;
	left: 0;
	background: rgba(0,0,0,0.4);
	z-index: 9999;
	display: flex;
	align-items: center;
	justify-content: center;
	visibility: hidden;
}

.sellkit-popup-box.sellkit-popup-active {
	visibility:visible;
}

.sellkit-popup-box h3 {
	margin-bottom: 20px;
}

.sellkit-popup-list {
	background: #fff;
	padding: 25px 15px;
	width: 700px;
	border-radius: 5px;
	height: 400px;
	overflow-x: hidden;
}

.sellkit-popup-list button {
	padding: 5px;
	background: #fff;
	border: 1px solid #d5dadf;
	color: #6d7882;
	border-radius: 5px;
	cursor: pointer;
	outline: none;
}

.sellkit-popup-list h5 {
	font-size:13px
}

.sellkit-popup-list li {
	margin-bottom: 15px;
	display: grid;
	grid-template-columns: 3fr 8fr 1fr;
	grid-gap: 5px;
	align-items: center;
}

.sellkit-popup-list .list-heading {
	border-bottom: 1px solid #d5dadf;
	padding-bottom: 15px;
	border-top: 1px solid #d5dadf;
	padding-top: 15px;
}

.sellkit-close-button {
	position: absolute;
	right: 25px;
	top: 25px;
	font-size: 25px;
	color: #fff;
	font-weight: 900;
	cursor: pointer;
}

.sellkit-popup-dark-theme .sellkit-popup-list {
	background-color: #404349 !important;
}

.sellkit-popup-dark-theme .sellkit-popup-list button {
	background-color: #34383C;
	border-color: #64666A;
	color: #E0E1E3;
}

@media (prefers-color-scheme: dark) {
	.sellkit-popup-auto-theme .sellkit-popup-list {
		background-color: #404349 !important;
	}

	.sellkit-popup-auto-theme .sellkit-popup-list button {
		background-color: #34383C;
		border-color: #64666A;
		color: #E0E1E3;
	}
}

</style>
<section id='sellkit-popup-box' class="sellkit-popup-box" >
	<span class="sellkit-close-button">&#x2715</span>
	<div class="sellkit-popup-list">
		<h3><?php echo esc_html__( 'Keywords List', 'sellkit' ); ?></h3>
		<ul>
			<li class="list-heading">
				<h4><?php echo esc_html__( 'Keywords Name', 'sellkit' ); ?></h4>
				<h4><?php echo esc_html__( 'Keywords Shortcode', 'sellkit' ); ?></h4>
			</li>
			<?php
			get_tags_title();
			?>
		</ul>
	</div>
</section>
