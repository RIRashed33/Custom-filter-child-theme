<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {
	wp_enqueue_style('hello-elementor-child-style', get_stylesheet_directory_uri() . '/style.css', ['hello-elementor-theme-style'], HELLO_ELEMENTOR_CHILD_VERSION);
	wp_enqueue_style('hello-elementor-child-responsive-style', get_stylesheet_directory_uri() . '/responsive.css', array(), HELLO_ELEMENTOR_CHILD_VERSION);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

// Candidates Filter Form Shortcode
add_shortcode('candidates_filter_form', 'candidates_filter_form');
function candidates_filter_form() {
	ob_start();
?>
<form class="employee-search-form" id="candidates_search_form">
	<div class="employee-form-content">
		<div class="form-fields-row">
			<div class="single-field">
				<label>
					<span>Location</span>
					<input type="text" name="location" placeholder="location">
				</label>
			</div>
			<div class="single-field">
				<label>
					<span>Travel radius</span>
					<select name="travel_radius">
						<option value="" selected disabled>Travel radius</option>
						<option value="5km">5km</option>
						<option value="10km">10km</option>
						<option value="15km">15km</option>
						<option value="20km">20km</option>
						<option value="25km">25km</option>
						<option value="30km">30km</option>
						<option value="50km+">50km+</option>
					</select>
				</label>
			</div>
			<div class="single-field">
				<label>
					<span>Industry</span>
					<select name="industry">
						<option value="" selected disabled>Industry</option>
						<option value="Fast Food">Fast Food</option>
						<option value="Cafes">Cafes</option>
						<option value="Restaurants">Restaurants</option>
						<option value="Retail">Retail</option>
						<option value="Customer Service">Customer Service</option>
						<option value="Management">Management</option>
					</select>
				</label>
			</div>
			<div class="single-field">
				<label>
					<span>Experience</span>
					<select name="experience">
						<option value="" selected disabled>Experience</option>
						<option value="No Experience">No Experience</option>
						<option value="1–2 Years">1–2 Years</option>
						<option value="3–5 Years">3–5 Years</option>
						<option value="5+ Years">5+ Years</option>
					</select>
				</label>
			</div>
			<div class="single-field">
				<label>
					<span>Availability</span>
					<select name="availability">
						<option value="" selected disabled>Availability</option>
						<option value="Weekdays">Weekdays</option>
						<option value="Evenings">Evenings</option>
						<option value="Weekends">Weekends</option>
						<option value="Public Holidays">Public Holidays</option>
					</select>
				</label>
			</div>
		</div>

		<div class="radio-fields-submit">
			<div class="single-radio-field">
				<span>Employment type:</span>
				<div class="filed-group">
					<label><input type="radio" name="employee_type" value="Full Time">Full Time</label>
					<label><input type="radio" name="employee_type" value="Part Time">Part Time</label>
					<label><input type="radio" name="employee_type" value="Casual">Casual</label>
				</div>
			</div>
			<div class="single-radio-field qualifications-field">
				<span>Qualifications:</span>
				<div class="qualifications">
					<div id="selected_qualifications"></div>
					<div class="qualification-selector">
						<button class="qualification-add-btn" id="qualification_add_btn" aria-haspopup="listbox" aria-expanded="false" type="button">Add</button>
						<ul class="qualification-dropdown" id="qualification_dropdown" style="display: none" role="listbox" aria-label="Select qualification">
							<li>Driver's Licence</li>
							<li>Vehicle</li>
							<li>RSA</li>
							<li>Food Safety Certificate</li>
							<li>Forklift</li>
							<li>Working With Children Check</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="single-radio-field form-submit">
				<button class="form-submit-btn" type="submit">Search</button>
			</div>
		</div>
	</div>
</form>
<script>
	const dropdown = document.getElementById('qualification_dropdown');
	const addBtn = document.getElementById('qualification_add_btn');
	const selectedContainer = document.getElementById('selected_qualifications');

	// Read all qualifications from the existing <li> elements
	const ALL_QUALS = Array.from(dropdown.querySelectorAll('li')).map(li => li.textContent.trim());
	let selected = [];
	let open = false;

	function render() {
		selectedContainer.innerHTML = '';
		selected.forEach(qualification => {
			const tag = document.createElement('button');
			tag.innerHTML = qualification;
			tag.type = 'button';
			tag.dataset.qualification = qualification;
			tag.name = 'qualifications[]';
			tag.value = qualification;
			selectedContainer.appendChild(tag);
		});

		const remaining = ALL_QUALS.filter(qualification => !selected.includes(qualification));
		dropdown.innerHTML = '';
		if (remaining.length === 0) {
			dropdown.innerHTML = '<li class="empty">No more options</li>';
		} else {
			remaining.forEach(qualification => {
				const li = document.createElement('li');
				li.setAttribute('role', 'option');
				li.textContent = qualification;
				li.addEventListener('click', () => {
					selected.push(qualification);
					closeDropdown();
					render();
				});
				dropdown.appendChild(li);
			});
		}
	}

	function closeDropdown() {
		open = false;
		dropdown.style.display = 'none';
		addBtn.setAttribute('aria-expanded', 'false');
	}

	addBtn.addEventListener('click', (e) => {
		e.stopPropagation();
		open = !open;
		dropdown.style.display = open ? 'block' : 'none';
		addBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
	});

	document.addEventListener('click', () => closeDropdown());
	dropdown.addEventListener('click', e => e.stopPropagation());

	selectedContainer.addEventListener('click', e => {
		const btn = e.target.closest('button[data-qualification]');
		if (btn) {
			selected = selected.filter(s => s !== btn.dataset.qualification);
			render();
		}
	});

	render();

	document.getElementById('candidates_search_form').addEventListener('submit', function(e) {
		e.preventDefault();
		const formData = new FormData(this);
		this.querySelectorAll('button[name="qualifications[]"]').forEach(btn => {
			formData.append('qualifications[]', btn.value);
		});
		formData.append('action', 'candidates_filter_ajax');

		const submitButton = this.querySelector('.form-submit-btn');
		this.classList.add('loading');
		submitButton.classList.add('loading');
		submitButton.disabled = true;
		
		fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
			method: 'POST',
			body: formData
		}).then(response => response.json()).then(response => {
			if (response.success) {
				const container = document.getElementById('filter_results_grid');
				container.innerHTML = response.data.html;
				if(response.data.more_page == 1){
					document.getElementById('load_more_container').innerHTML = `<button id="load_more_candidates" class="load-more-btn" type="button" data-page="${response.data.page}">Load More</button>`;
				}else{
					document.getElementById('load_more_container').innerHTML = '';
				}
			}
		}).catch(error => {
			console.error(error);
		}).finally(() => {
			this.classList.remove('loading');
			submitButton.classList.remove('loading');
			submitButton.disabled = false;
		});
	});
</script>
<?php
	return ob_get_clean();
}

// Candidates Filter Result Shortcode
add_shortcode('candidates_filter_result', 'candidates_filter_result');
function candidates_filter_result() {
	$page  = 1;
	$limit = 9;
	$user_query = new WP_User_Query([
		'number' => $limit,
		'paged'  => $page,
		'meta_query' => [
			[
				'key'     => 'account_type',
				'value'   => '"Candidate"',
				'compare' => 'LIKE',
			],
		],
	]);
	
	$users = $user_query->get_results();
	$total_users = $user_query->get_total();
	$total_pages = ceil( $total_users / $limit );
	ob_start();
?>
<div class="filter-results-content" id="filter_results_content">
	<?php if(is_array($users) && !empty($users)) : ?>
	<div class="filter-results-grid" id="filter_results_grid">
		<?php foreach($users as $user) : ?>
		<div class="single-employee-card">
			<div class="employee-card-header">
				<div class="employee-bio">
					<div class="employee-img">
						<?php echo get_avatar( $user->ID, 120 ); ?>
					</div>
					<div class="employee-title">
						<h3><?php $full_name = trim( $user->first_name . ' ' . $user->last_name ); echo !empty( $full_name ) ? $full_name : $user->display_name; ?></h3>
						<h6>Crew Member</h6>
					</div>
					<div class="employee-badge">
						<svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.6667 0H1C0.734783 0 0.48043 0.105357 0.292893 0.292893C0.105357 0.48043 0 0.734784 0 1V12.5C0.000260967 12.7189 0.063176 12.9332 0.181311 13.1175C0.299446 13.3017 0.467865 13.4484 0.666667 13.54C0.827026 13.6241 1.00558 13.6676 1.18667 13.6667C1.4275 13.6588 1.66015 13.5774 1.85333 13.4333L5.42 10.7667C5.5354 10.6801 5.67575 10.6333 5.82 10.6333C5.96425 10.6333 6.1046 10.6801 6.22 10.7667L9.78667 13.4333C9.96 13.5633 10.1661 13.6425 10.3819 13.662C10.5977 13.6814 10.8146 13.6404 11.0084 13.5435C11.2022 13.4466 11.3652 13.2977 11.4791 13.1134C11.593 12.929 11.6533 12.7167 11.6533 12.5V1C11.6534 0.737078 11.5498 0.484724 11.3652 0.297566C11.1805 0.110408 10.9296 0.00350563 10.6667 0Z" fill="currentColor"/></svg>
					</div>
				</div>
				<div class="employee-availability">
					<span>
						<svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M5.70748 11.8434C5.77268 11.9412 5.88246 12 6 12C6.11754 12 6.22732 11.9413 6.29252 11.8434C7.12453 10.5955 8.35001 9.05419 9.20398 7.48666C9.8868 6.23332 10.2188 5.16436 10.2188 4.21875C10.2188 1.89253 8.32622 0 6 0C3.67378 0 1.78125 1.89253 1.78125 4.21875C1.78125 5.16436 2.1132 6.23332 2.79602 7.48666C3.64936 9.05299 4.87718 10.598 5.70748 11.8434ZM6 0.703125C7.93852 0.703125 9.51562 2.28023 9.51562 4.21875C9.51562 5.04384 9.21171 6.00277 8.58654 7.15029C7.85044 8.50144 6.80002 9.87204 6 11.0243C5.2001 9.8722 4.14961 8.50151 3.41346 7.15029C2.78829 6.00277 2.48438 5.04384 2.48438 4.21875C2.48438 2.28023 4.06148 0.703125 6 0.703125Z" fill="black"/><path d="M6 6.32812C7.16311 6.32812 8.10938 5.38186 8.10938 4.21875C8.10938 3.05564 7.16311 2.10938 6 2.10938C4.83689 2.10938 3.89062 3.05564 3.89062 4.21875C3.89062 5.38186 4.83689 6.32812 6 6.32812ZM6 2.8125C6.77541 2.8125 7.40625 3.44334 7.40625 4.21875C7.40625 4.99416 6.77541 5.625 6 5.625C5.22459 5.625 4.59375 4.99416 4.59375 4.21875C4.59375 3.44334 5.22459 2.8125 6 2.8125Z" fill="currentColor"/></svg>
						<?php echo get_user_meta( $user->ID, 'your_location', true ) ?: 'Not set'; ?>
					</span>
					<span>
						<svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_387_1892)"><path d="M2.17928 2.54299L2.5285 3.26487L3.32069 3.37268C3.47772 3.39378 3.53397 3.58831 3.42616 3.69143L2.84725 4.2469L2.99022 5.0344C3.01834 5.18909 2.85428 5.30628 2.71834 5.23128L2.01288 4.85159L1.30741 5.23128C1.17147 5.30393 1.00038 5.19143 1.03788 5.02268L1.1785 4.24456L0.599595 3.68909C0.487095 3.58128 0.548032 3.38909 0.707407 3.36799L1.49725 3.26018L1.84647 2.53831C1.91209 2.40003 2.11366 2.40237 2.17928 2.54299ZM2.23553 3.518L2.01288 3.05393L1.78788 3.518C1.76209 3.5719 1.70819 3.61409 1.64491 3.62346L1.13631 3.69143L1.50897 4.04768C1.55116 4.08987 1.57459 4.15081 1.56288 4.21409L1.47147 4.72034L1.92147 4.47893C1.97538 4.45081 2.04334 4.44612 2.09959 4.47893L2.55194 4.72268L2.46288 4.22581C2.44881 4.16487 2.46522 4.0969 2.51444 4.04768L2.88709 3.69143L2.38319 3.62346C2.32225 3.61643 2.26366 3.57659 2.23553 3.518ZM6.16834 0.843776L6.51756 1.56565L7.3121 1.67346C7.46913 1.69456 7.52538 1.88909 7.41756 1.99456L6.83866 2.55003L6.98163 3.33753C7.00975 3.49221 6.84569 3.6094 6.70975 3.5344L5.99959 3.15471L5.29413 3.5344C5.15819 3.60706 4.98709 3.49456 5.02459 3.32581L5.16522 2.54768L4.58397 1.99221C4.47147 1.8844 4.53241 1.69221 4.69178 1.67112L5.48163 1.56331L5.83084 0.843776C5.90116 0.700807 6.10038 0.703151 6.16834 0.843776ZM6.2246 1.81878L5.99959 1.35471L5.77694 1.81878C5.75116 1.87268 5.69725 1.91487 5.63397 1.92424L5.12303 1.99221L5.49569 2.34846C5.54022 2.39065 5.56131 2.45159 5.54959 2.51487L5.45819 3.02112L5.91053 2.77971C5.96444 2.75159 6.03006 2.7469 6.08866 2.77971L6.541 3.02346L6.45194 2.52659C6.43788 2.46565 6.45428 2.39768 6.5035 2.34846L6.87616 1.99221L6.37225 1.9219C6.30897 1.91721 6.25272 1.87737 6.2246 1.81878ZM10.1551 2.54299L10.5043 3.26487L11.2965 3.37268C11.4535 3.39378 11.5121 3.58831 11.4019 3.69143L10.823 4.2469L10.966 5.0344C10.9941 5.18909 10.8301 5.30628 10.6941 5.23128L9.98866 4.85159L9.28319 5.23128C9.14725 5.30393 8.97616 5.19143 9.01131 5.02268L9.15194 4.24456L8.57303 3.68909C8.46053 3.58128 8.52147 3.38909 8.68085 3.36799L9.47069 3.26018L9.81991 2.53831C9.88788 2.40003 10.0894 2.40237 10.1551 2.54299ZM10.2113 3.518L9.98866 3.05393L9.76366 3.518C9.73788 3.5719 9.68631 3.61409 9.62069 3.62346L9.11209 3.69143L9.48241 4.04768C9.5246 4.08987 9.54803 4.15081 9.53631 4.21409L9.44491 4.72034L9.89725 4.47893C9.95116 4.45081 10.0191 4.44612 10.0754 4.47893L10.5301 4.72268L10.441 4.22581C10.4269 4.16487 10.4433 4.0969 10.4926 4.04768L10.8629 3.69143L10.359 3.62346C10.298 3.61643 10.2394 3.57659 10.2113 3.518ZM5.13006 7.32893C5.0785 7.23987 5.10897 7.12503 5.19803 7.07346C5.2871 7.0219 5.40194 7.05237 5.4535 7.14143L5.69959 7.56799L6.57381 6.69378C6.64647 6.62112 6.766 6.62112 6.83866 6.69378C6.91131 6.76643 6.91131 6.88596 6.83866 6.95862L5.791 8.00628C5.70428 8.09299 5.55897 8.07424 5.49803 7.96878L5.13006 7.32893ZM5.99959 5.34846C7.01913 5.34846 7.84647 6.17581 7.84647 7.19534C7.84647 8.21487 7.01913 9.04221 5.99959 9.04221C4.98006 9.04221 4.15272 8.21487 4.15272 7.19534C4.15272 6.17581 4.98006 5.34846 5.99959 5.34846ZM7.04022 6.15471C6.466 5.5805 5.53319 5.5805 4.95897 6.15471C4.38475 6.72893 4.38475 7.66174 4.95897 8.23596C5.53319 8.81018 6.466 8.81018 7.04022 8.23596C7.61444 7.6594 7.61444 6.72893 7.04022 6.15471ZM5.99959 4.18831C7.65897 4.18831 9.00428 5.53362 9.00428 7.19299C9.00428 7.82112 8.81209 8.40471 8.48163 8.88753L9.37928 10.4414C9.466 10.5867 9.34647 10.7508 9.19178 10.7274L8.27303 10.5821L7.78085 11.1867C7.69413 11.2992 7.53241 11.2805 7.46913 11.168L6.841 10.0805C6.29256 10.2399 5.70428 10.2399 5.15819 10.0805L4.53006 11.168C4.46678 11.2805 4.30506 11.2992 4.21834 11.1867L3.72616 10.5821L2.80741 10.7274C2.65038 10.7532 2.54022 10.5821 2.61522 10.4485L3.51756 8.88753C2.70194 7.69456 2.85428 6.08909 3.87616 5.06721C4.41756 4.52581 5.16991 4.18831 5.99959 4.18831ZM8.23319 9.20393C7.94725 9.52268 7.59569 9.77815 7.19725 9.95159L7.65663 10.7461L8.05038 10.2633C8.09491 10.2071 8.16288 10.186 8.2285 10.1977L8.86131 10.2985L8.23319 9.20393ZM4.79959 9.95159C4.4035 9.77815 4.05194 9.52268 3.766 9.20628L3.13553 10.2985L3.76834 10.1977C3.83163 10.186 3.90194 10.2094 3.94647 10.2633L4.34022 10.7461L4.79959 9.95159ZM7.86053 5.3344C6.83397 4.30784 5.16756 4.30784 4.141 5.3344C3.11444 6.36096 3.11444 8.02737 4.141 9.05393C5.16991 10.0805 6.83397 10.0805 7.86053 9.05393C8.8871 8.02737 8.8871 6.36096 7.86053 5.3344Z" fill="black"/></g><defs><clipPath id="clip0_387_1892"><rect width="12" height="12" fill="currentColor"/></clipPath></defs></svg>
						<?php echo get_user_meta( $user->ID, 'your_experience_level', true ) ?: 'Not set'; ?>
					</span>
					<span>
						<svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M9.86872 9.72288C9.77164 9.72288 9.69294 9.80159 9.69294 9.89866V11.5513C9.69294 11.6048 9.64939 11.6484 9.59588 11.6484H8.65095V8.18674C8.65095 8.08966 8.57225 8.01096 8.47517 8.01096C8.37809 8.01096 8.29939 8.08966 8.29939 8.18674V11.6484H6.39662L6.94841 11.0414C7.00869 10.9751 7.03712 10.8806 7.02444 10.7886C7.0242 10.7868 7.02392 10.785 7.02362 10.7832L6.49808 7.67852L6.63966 7.46538C6.71795 7.52098 6.77825 7.57451 6.89134 7.57451C7.00637 7.57451 7.11392 7.51013 7.16684 7.40414V7.40412L7.72114 6.29318L8.98386 6.7822C9.02954 6.8018 9.69289 7.10053 9.69289 7.88934V9.07833C9.69287 9.17538 9.77157 9.25411 9.86867 9.25411C9.96575 9.25411 10.0445 9.17541 10.0445 9.07833V7.88934C10.0445 6.85003 9.15456 6.47224 9.11668 6.45666C9.11558 6.45619 9.11445 6.45574 9.11333 6.45532L7.01173 5.64143V5.14854C7.34553 4.88895 7.57698 4.50413 7.63508 4.06509C7.80151 4.05338 7.95594 3.99122 8.08206 3.88277C8.24675 3.74114 8.34125 3.53501 8.34125 3.31732C8.34125 3.13666 8.27607 2.96416 8.15991 2.82975V2.17859C8.15989 0.97732 7.18259 0 5.98133 0C4.78006 0 3.80277 0.97732 3.80277 2.17859V2.84681C3.68537 2.97987 3.62152 3.14365 3.62152 3.3173C3.62152 3.53505 3.71598 3.74116 3.88072 3.88282C4.00674 3.99122 4.16108 4.0533 4.32746 4.06505C4.38603 4.5123 4.62423 4.91147 4.98645 5.17455V5.64143L2.88713 6.4553C2.88603 6.45572 2.88493 6.45616 2.88383 6.45661C2.84593 6.4722 1.95605 6.84998 1.95605 7.8893V11.5513C1.95605 11.7987 2.15731 11.9999 2.4047 11.9999H4.07947C4.17655 11.9999 4.25525 11.9212 4.25525 11.8241C4.25525 11.7271 4.17655 11.6484 4.07947 11.6484H3.70126V8.18674C3.70126 8.08966 3.62255 8.01096 3.52548 8.01096C3.4284 8.01096 3.3497 8.08966 3.3497 8.18674V11.6484H2.40472C2.35121 11.6484 2.30766 11.6049 2.30766 11.5514V7.88932C2.30766 7.09786 2.97547 6.79981 3.01648 6.78227L4.27822 6.29313L4.83256 7.40412C4.8856 7.51036 4.9933 7.57451 5.10809 7.57451C5.22062 7.57451 5.28034 7.5218 5.35977 7.46538L5.50135 7.67852L4.97586 10.7832C4.97555 10.785 4.9753 10.7868 4.97504 10.7885C4.96234 10.8805 4.99077 10.975 5.05107 11.0414L5.60286 11.6484H4.89985C4.80277 11.6484 4.72407 11.7271 4.72407 11.8242C4.72407 11.9213 4.80277 12 4.89985 12H9.59591C9.84329 12 10.0445 11.7987 10.0445 11.5514V9.89869C10.0445 9.80159 9.9658 9.72288 9.86872 9.72288ZM7.39187 6.16568L6.87509 7.20138C6.86225 7.19227 6.58838 6.99769 6.2536 6.75996C6.39887 6.58064 6.22892 6.79041 6.89192 5.97206L7.39187 6.16568ZM6.35284 7.26171L6.21882 7.46344H5.78047L5.64645 7.26171L5.99966 7.01086L6.35284 7.26171ZM5.33799 5.69815V5.36918C5.55155 5.45527 5.77979 5.49518 6.00268 5.49518C6.23314 5.49518 6.45915 5.44605 6.66015 5.35816V5.69958L5.99966 6.51485L5.33799 5.69815ZM7.98966 3.31732C7.98964 3.43247 7.93977 3.54143 7.85281 3.61622C7.79532 3.66567 7.72588 3.69729 7.64982 3.70966V2.92505C7.85185 2.95786 7.98966 3.12544 7.98966 3.31732ZM4.31293 3.70966C4.1086 3.6765 3.97309 3.50756 3.97309 3.3173C3.97309 3.12937 4.12888 2.97642 4.31293 2.93388V3.70966ZM4.32238 2.57569C4.26638 2.58211 4.20221 2.59727 4.15438 2.61354V2.17859C4.15438 1.17117 4.97394 0.351586 5.98135 0.351586C6.98877 0.351586 7.80833 1.17117 7.80833 2.17859V2.60147C7.75724 2.58593 7.70424 2.57566 7.64984 2.57084C7.64984 2.40122 7.65132 2.43394 7.09644 1.83738C6.99439 1.72774 6.82559 1.70477 6.69713 1.7887C6.08471 2.18876 5.33764 2.38973 4.64834 2.34023C4.49715 2.32891 4.35943 2.4285 4.32238 2.57569ZM4.66452 3.84806V2.75137V2.6936C5.4233 2.73804 6.21884 2.51262 6.86152 2.10103C7.11601 2.37427 7.25382 2.51759 7.29826 2.5721C7.29826 2.71001 7.29826 3.69019 7.29826 3.84804C7.29826 4.56164 6.7164 5.14357 6.0027 5.14357C5.20128 5.14359 4.66452 4.52189 4.66452 3.84806ZM5.12424 7.20138L4.60747 6.16568L5.10652 5.97239L5.74498 6.76048C5.42031 6.99103 5.1537 7.18045 5.12424 7.20138ZM5.32592 10.8211L5.83473 7.81498H6.16452L6.67332 10.8211L5.99963 11.5623L5.32592 10.8211Z" fill="currentColor"/></svg>
						<?php
							$employment_preference = get_user_meta( $user->ID, 'your_employment_preference', true );
							if ( ! empty( $employment_preference ) && is_array( $employment_preference ) ) {
								echo esc_html( implode( ', ', $employment_preference ) );
							}
						?>
					</span>
				</div>
			</div>
			<div class="employee-card-footer">
				<div class="experience-details">
					<div class="single-info">
						<p>Industry</p>
						<p class="industry">
							<?php
							$categories = get_user_meta( $user->ID, 'your_industry_categories', true );
							if ( ! empty( $categories ) && is_array( $categories ) ) {
								echo esc_html( implode( ', ', $categories ) );
							}
							?>
						</p>
					</div>
					<div class="single-info">
						<p>Qualifications:</p>
						<div class="qualification-list">
							<?php
							$qualifications = get_user_meta( $user->ID, 'your_qualifications', true );
							if ( !empty( $qualifications ) && is_array($qualifications) ) {
								foreach($qualifications as $qualification) {
									echo '<span>' . $qualification . '</span>';
								}
							}
							?>
						</div>
					</div>
					<div class="single-info">
						<p>Travel radius</p>
						<p><?php echo get_user_meta( $user->ID, 'your_travel_distance', true ) ?: 'Not set'; ?></p>
					</div>
				</div>
				<div class="employee-buttons">
					<a href="<?php echo get_author_posts_url( $user->ID )?: '#'; ?>" class="view-profile-btn">View Profile</a>
					<a href="#" class="message-btn">Message</a>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="load-more" id="load_more_container">
		<?php if($total_pages > $page) : ?>
		<button id="load_more_candidates" class="load-more-btn" type="button" data-page="<?php echo $page + 1; ?>">Load More</button>
		<?php endif; ?>
	</div>
	<?php else : ?>
	<div class="empty-candidate-container">
		<p>No candidate profiles found.</p>
	</div>
	<?php endif; ?>
</div>
<script>
	document.addEventListener('click', function (e) {
		if (e.target.id === 'load_more_candidates') {
			e.preventDefault();
			const button = e.target;
			const page = parseInt(button.getAttribute('data-page'), 10) || 1;
			
			const formData = new FormData(
				document.getElementById('candidates_search_form')
			);

			document.querySelectorAll(
				'#candidates_search_form button[name="qualifications[]"]'
			).forEach(btn => {
				formData.append('qualifications[]', btn.value);
			});

			formData.append('action', 'candidates_filter_ajax');
			formData.append('page', page);
			
			button.classList.add('loading');
			button.disabled = true;
			fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
				method: 'POST',
				body: formData
			}).then(response => response.json()).then(response => {
				if (response.success) {
					const container = document.getElementById('filter_results_grid');
					container.innerHTML += response.data.html;
					
					button.classList.remove('loading');
					button.disabled = false;
					
					if(response.data.more_page == 1){
						document.getElementById('load_more_container').innerHTML = `<button id="load_more_candidates" class="load-more-btn" type="button" data-page="${response.data.page}">Load More</button>`;
					}else{
						document.getElementById('load_more_container').innerHTML = '';
					}
				}
			}).catch(error => {
				console.error(error);
			});
		}
	});
</script>
<?php
	return ob_get_clean();
}

// Candidates Filter Result Ajax
add_action( 'wp_ajax_candidates_filter_ajax', 'candidates_filter_ajax' );
add_action( 'wp_ajax_nopriv_candidates_filter_ajax', 'candidates_filter_ajax' );
function candidates_filter_ajax() {
	$page  = max( 1, absint( $_POST['page'] ?? 1 ) );
	$limit = 9;

	$meta_query = [
		'relation' => 'AND',
		[
			'key'     => 'account_type',
			'value'   => '"Candidate"',
			'compare' => 'LIKE',
		],
	];
	
	if ( ! empty( $_POST['location'] ) ) {

		$meta_query[] = [
			'key'     => 'your_location',
			'value'   => sanitize_text_field( $_POST['location'] ),
			'compare' => 'LIKE',
		];
	}

	if (!empty( $_POST['travel_radius'] )) {
		$radius = sanitize_text_field( $_POST['travel_radius'] );
		$radius_map = [
			'5km'   => ['5km'],
			'10km'  => ['5km','10km'],
			'15km'  => ['5km','10km','15km'],
			'20km'  => ['5km','10km','15km','20km'],
			'25km'  => ['5km','10km','15km','20km','25km'],
			'30km'  => ['5km','10km','15km','20km','25km','30km'],
			'50km+' => ['5km','10km','15km','20km','25km','30km','50km+'],
		];

		if ( isset( $radius_map[ $radius ] ) ) {
			$travel_query = [
				'relation' => 'OR',
			];
			foreach ( $radius_map[ $radius ] as $value ) {
				$travel_query[] = [
					'key'   => 'your_travel_distance',
					'value' => $value,
				];
			}
			$meta_query[] = $travel_query;
		}
	}

	if ( ! empty( $_POST['industry'] ) ) {
		$meta_query[] = [
			'key'     => 'your_industry_categories',
			'value'   => '"' . sanitize_text_field( $_POST['industry'] ) . '"',
			'compare' => 'LIKE',
		];
	}
	
	if(!empty( $_POST['experience'])) {
		$meta_query[] = [
			'key'   => 'your_experience_level',
			'value' => sanitize_text_field( $_POST['experience'] ),
		];
	}
	
	if ( ! empty( $_POST['availability'] ) ) {
		$meta_query[] = [
			'key'     => 'your_availability',
			'value'   => '"' . sanitize_text_field( $_POST['availability'] ) . '"',
			'compare' => 'LIKE',
		];
	}
	
	if ( ! empty( $_POST['employee_type'] ) ) {
		$meta_query[] = [
			'key'     => 'your_employment_preference',
			'value'   => '"' . sanitize_text_field( $_POST['employee_type'] ) . '"',
			'compare' => 'LIKE',
		];
	}

	if ( ! empty( $_POST['qualifications'] ) ) {
		$qualification_query = [
			'relation' => 'OR',
		];
		foreach ( (array) $_POST['qualifications'] as $qualification ) {

			$qualification_query[] = [
				'key'     => 'your_qualifications',
				'value'   => '"' . sanitize_text_field( $qualification ) . '"',
				'compare' => 'LIKE',
			];
		}
		$meta_query[] = $qualification_query;
	}

	$args = [
		'number'     => $limit,
		'paged'      => $page,
		'meta_query' => $meta_query,
	];

	$user_query = new WP_User_Query( $args );
	$users = $user_query->get_results();
	$total_users = $user_query->get_total();
	$total_pages = ceil( $total_users / $limit );
	$more_page = 0;
	if($total_pages > $page){
		$more_page = 1;
	}
	
    ob_start();
    if(!empty($users)) :
	foreach($users as $user) :
	?>
	<div class="single-employee-card">
		<div class="employee-card-header">
			<div class="employee-bio">
				<div class="employee-img">
					<?php echo get_avatar( $user->ID, 120 ); ?>
				</div>
				<div class="employee-title">
					<h3><?php $full_name = trim( $user->first_name . ' ' . $user->last_name ); echo !empty( $full_name ) ? $full_name : $user->display_name; ?></h3>
					<h6>Crew Member</h6>
				</div>
				<div class="employee-badge">
					<svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.6667 0H1C0.734783 0 0.48043 0.105357 0.292893 0.292893C0.105357 0.48043 0 0.734784 0 1V12.5C0.000260967 12.7189 0.063176 12.9332 0.181311 13.1175C0.299446 13.3017 0.467865 13.4484 0.666667 13.54C0.827026 13.6241 1.00558 13.6676 1.18667 13.6667C1.4275 13.6588 1.66015 13.5774 1.85333 13.4333L5.42 10.7667C5.5354 10.6801 5.67575 10.6333 5.82 10.6333C5.96425 10.6333 6.1046 10.6801 6.22 10.7667L9.78667 13.4333C9.96 13.5633 10.1661 13.6425 10.3819 13.662C10.5977 13.6814 10.8146 13.6404 11.0084 13.5435C11.2022 13.4466 11.3652 13.2977 11.4791 13.1134C11.593 12.929 11.6533 12.7167 11.6533 12.5V1C11.6534 0.737078 11.5498 0.484724 11.3652 0.297566C11.1805 0.110408 10.9296 0.00350563 10.6667 0Z" fill="currentColor"/></svg>
				</div>
			</div>
			<div class="employee-availability">
				<span>
					<svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M5.70748 11.8434C5.77268 11.9412 5.88246 12 6 12C6.11754 12 6.22732 11.9413 6.29252 11.8434C7.12453 10.5955 8.35001 9.05419 9.20398 7.48666C9.8868 6.23332 10.2188 5.16436 10.2188 4.21875C10.2188 1.89253 8.32622 0 6 0C3.67378 0 1.78125 1.89253 1.78125 4.21875C1.78125 5.16436 2.1132 6.23332 2.79602 7.48666C3.64936 9.05299 4.87718 10.598 5.70748 11.8434ZM6 0.703125C7.93852 0.703125 9.51562 2.28023 9.51562 4.21875C9.51562 5.04384 9.21171 6.00277 8.58654 7.15029C7.85044 8.50144 6.80002 9.87204 6 11.0243C5.2001 9.8722 4.14961 8.50151 3.41346 7.15029C2.78829 6.00277 2.48438 5.04384 2.48438 4.21875C2.48438 2.28023 4.06148 0.703125 6 0.703125Z" fill="black"/><path d="M6 6.32812C7.16311 6.32812 8.10938 5.38186 8.10938 4.21875C8.10938 3.05564 7.16311 2.10938 6 2.10938C4.83689 2.10938 3.89062 3.05564 3.89062 4.21875C3.89062 5.38186 4.83689 6.32812 6 6.32812ZM6 2.8125C6.77541 2.8125 7.40625 3.44334 7.40625 4.21875C7.40625 4.99416 6.77541 5.625 6 5.625C5.22459 5.625 4.59375 4.99416 4.59375 4.21875C4.59375 3.44334 5.22459 2.8125 6 2.8125Z" fill="currentColor"/></svg>
					<?php echo get_user_meta( $user->ID, 'your_location', true ) ?: 'Not set'; ?>
				</span>
				<span>
					<svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_387_1892)"><path d="M2.17928 2.54299L2.5285 3.26487L3.32069 3.37268C3.47772 3.39378 3.53397 3.58831 3.42616 3.69143L2.84725 4.2469L2.99022 5.0344C3.01834 5.18909 2.85428 5.30628 2.71834 5.23128L2.01288 4.85159L1.30741 5.23128C1.17147 5.30393 1.00038 5.19143 1.03788 5.02268L1.1785 4.24456L0.599595 3.68909C0.487095 3.58128 0.548032 3.38909 0.707407 3.36799L1.49725 3.26018L1.84647 2.53831C1.91209 2.40003 2.11366 2.40237 2.17928 2.54299ZM2.23553 3.518L2.01288 3.05393L1.78788 3.518C1.76209 3.5719 1.70819 3.61409 1.64491 3.62346L1.13631 3.69143L1.50897 4.04768C1.55116 4.08987 1.57459 4.15081 1.56288 4.21409L1.47147 4.72034L1.92147 4.47893C1.97538 4.45081 2.04334 4.44612 2.09959 4.47893L2.55194 4.72268L2.46288 4.22581C2.44881 4.16487 2.46522 4.0969 2.51444 4.04768L2.88709 3.69143L2.38319 3.62346C2.32225 3.61643 2.26366 3.57659 2.23553 3.518ZM6.16834 0.843776L6.51756 1.56565L7.3121 1.67346C7.46913 1.69456 7.52538 1.88909 7.41756 1.99456L6.83866 2.55003L6.98163 3.33753C7.00975 3.49221 6.84569 3.6094 6.70975 3.5344L5.99959 3.15471L5.29413 3.5344C5.15819 3.60706 4.98709 3.49456 5.02459 3.32581L5.16522 2.54768L4.58397 1.99221C4.47147 1.8844 4.53241 1.69221 4.69178 1.67112L5.48163 1.56331L5.83084 0.843776C5.90116 0.700807 6.10038 0.703151 6.16834 0.843776ZM6.2246 1.81878L5.99959 1.35471L5.77694 1.81878C5.75116 1.87268 5.69725 1.91487 5.63397 1.92424L5.12303 1.99221L5.49569 2.34846C5.54022 2.39065 5.56131 2.45159 5.54959 2.51487L5.45819 3.02112L5.91053 2.77971C5.96444 2.75159 6.03006 2.7469 6.08866 2.77971L6.541 3.02346L6.45194 2.52659C6.43788 2.46565 6.45428 2.39768 6.5035 2.34846L6.87616 1.99221L6.37225 1.9219C6.30897 1.91721 6.25272 1.87737 6.2246 1.81878ZM10.1551 2.54299L10.5043 3.26487L11.2965 3.37268C11.4535 3.39378 11.5121 3.58831 11.4019 3.69143L10.823 4.2469L10.966 5.0344C10.9941 5.18909 10.8301 5.30628 10.6941 5.23128L9.98866 4.85159L9.28319 5.23128C9.14725 5.30393 8.97616 5.19143 9.01131 5.02268L9.15194 4.24456L8.57303 3.68909C8.46053 3.58128 8.52147 3.38909 8.68085 3.36799L9.47069 3.26018L9.81991 2.53831C9.88788 2.40003 10.0894 2.40237 10.1551 2.54299ZM10.2113 3.518L9.98866 3.05393L9.76366 3.518C9.73788 3.5719 9.68631 3.61409 9.62069 3.62346L9.11209 3.69143L9.48241 4.04768C9.5246 4.08987 9.54803 4.15081 9.53631 4.21409L9.44491 4.72034L9.89725 4.47893C9.95116 4.45081 10.0191 4.44612 10.0754 4.47893L10.5301 4.72268L10.441 4.22581C10.4269 4.16487 10.4433 4.0969 10.4926 4.04768L10.8629 3.69143L10.359 3.62346C10.298 3.61643 10.2394 3.57659 10.2113 3.518ZM5.13006 7.32893C5.0785 7.23987 5.10897 7.12503 5.19803 7.07346C5.2871 7.0219 5.40194 7.05237 5.4535 7.14143L5.69959 7.56799L6.57381 6.69378C6.64647 6.62112 6.766 6.62112 6.83866 6.69378C6.91131 6.76643 6.91131 6.88596 6.83866 6.95862L5.791 8.00628C5.70428 8.09299 5.55897 8.07424 5.49803 7.96878L5.13006 7.32893ZM5.99959 5.34846C7.01913 5.34846 7.84647 6.17581 7.84647 7.19534C7.84647 8.21487 7.01913 9.04221 5.99959 9.04221C4.98006 9.04221 4.15272 8.21487 4.15272 7.19534C4.15272 6.17581 4.98006 5.34846 5.99959 5.34846ZM7.04022 6.15471C6.466 5.5805 5.53319 5.5805 4.95897 6.15471C4.38475 6.72893 4.38475 7.66174 4.95897 8.23596C5.53319 8.81018 6.466 8.81018 7.04022 8.23596C7.61444 7.6594 7.61444 6.72893 7.04022 6.15471ZM5.99959 4.18831C7.65897 4.18831 9.00428 5.53362 9.00428 7.19299C9.00428 7.82112 8.81209 8.40471 8.48163 8.88753L9.37928 10.4414C9.466 10.5867 9.34647 10.7508 9.19178 10.7274L8.27303 10.5821L7.78085 11.1867C7.69413 11.2992 7.53241 11.2805 7.46913 11.168L6.841 10.0805C6.29256 10.2399 5.70428 10.2399 5.15819 10.0805L4.53006 11.168C4.46678 11.2805 4.30506 11.2992 4.21834 11.1867L3.72616 10.5821L2.80741 10.7274C2.65038 10.7532 2.54022 10.5821 2.61522 10.4485L3.51756 8.88753C2.70194 7.69456 2.85428 6.08909 3.87616 5.06721C4.41756 4.52581 5.16991 4.18831 5.99959 4.18831ZM8.23319 9.20393C7.94725 9.52268 7.59569 9.77815 7.19725 9.95159L7.65663 10.7461L8.05038 10.2633C8.09491 10.2071 8.16288 10.186 8.2285 10.1977L8.86131 10.2985L8.23319 9.20393ZM4.79959 9.95159C4.4035 9.77815 4.05194 9.52268 3.766 9.20628L3.13553 10.2985L3.76834 10.1977C3.83163 10.186 3.90194 10.2094 3.94647 10.2633L4.34022 10.7461L4.79959 9.95159ZM7.86053 5.3344C6.83397 4.30784 5.16756 4.30784 4.141 5.3344C3.11444 6.36096 3.11444 8.02737 4.141 9.05393C5.16991 10.0805 6.83397 10.0805 7.86053 9.05393C8.8871 8.02737 8.8871 6.36096 7.86053 5.3344Z" fill="black"/></g><defs><clipPath id="clip0_387_1892"><rect width="12" height="12" fill="currentColor"/></clipPath></defs></svg>
					<?php echo get_user_meta( $user->ID, 'your_experience_level', true ) ?: 'Not set'; ?>
				</span>
				<span>
					<svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M9.86872 9.72288C9.77164 9.72288 9.69294 9.80159 9.69294 9.89866V11.5513C9.69294 11.6048 9.64939 11.6484 9.59588 11.6484H8.65095V8.18674C8.65095 8.08966 8.57225 8.01096 8.47517 8.01096C8.37809 8.01096 8.29939 8.08966 8.29939 8.18674V11.6484H6.39662L6.94841 11.0414C7.00869 10.9751 7.03712 10.8806 7.02444 10.7886C7.0242 10.7868 7.02392 10.785 7.02362 10.7832L6.49808 7.67852L6.63966 7.46538C6.71795 7.52098 6.77825 7.57451 6.89134 7.57451C7.00637 7.57451 7.11392 7.51013 7.16684 7.40414V7.40412L7.72114 6.29318L8.98386 6.7822C9.02954 6.8018 9.69289 7.10053 9.69289 7.88934V9.07833C9.69287 9.17538 9.77157 9.25411 9.86867 9.25411C9.96575 9.25411 10.0445 9.17541 10.0445 9.07833V7.88934C10.0445 6.85003 9.15456 6.47224 9.11668 6.45666C9.11558 6.45619 9.11445 6.45574 9.11333 6.45532L7.01173 5.64143V5.14854C7.34553 4.88895 7.57698 4.50413 7.63508 4.06509C7.80151 4.05338 7.95594 3.99122 8.08206 3.88277C8.24675 3.74114 8.34125 3.53501 8.34125 3.31732C8.34125 3.13666 8.27607 2.96416 8.15991 2.82975V2.17859C8.15989 0.97732 7.18259 0 5.98133 0C4.78006 0 3.80277 0.97732 3.80277 2.17859V2.84681C3.68537 2.97987 3.62152 3.14365 3.62152 3.3173C3.62152 3.53505 3.71598 3.74116 3.88072 3.88282C4.00674 3.99122 4.16108 4.0533 4.32746 4.06505C4.38603 4.5123 4.62423 4.91147 4.98645 5.17455V5.64143L2.88713 6.4553C2.88603 6.45572 2.88493 6.45616 2.88383 6.45661C2.84593 6.4722 1.95605 6.84998 1.95605 7.8893V11.5513C1.95605 11.7987 2.15731 11.9999 2.4047 11.9999H4.07947C4.17655 11.9999 4.25525 11.9212 4.25525 11.8241C4.25525 11.7271 4.17655 11.6484 4.07947 11.6484H3.70126V8.18674C3.70126 8.08966 3.62255 8.01096 3.52548 8.01096C3.4284 8.01096 3.3497 8.08966 3.3497 8.18674V11.6484H2.40472C2.35121 11.6484 2.30766 11.6049 2.30766 11.5514V7.88932C2.30766 7.09786 2.97547 6.79981 3.01648 6.78227L4.27822 6.29313L4.83256 7.40412C4.8856 7.51036 4.9933 7.57451 5.10809 7.57451C5.22062 7.57451 5.28034 7.5218 5.35977 7.46538L5.50135 7.67852L4.97586 10.7832C4.97555 10.785 4.9753 10.7868 4.97504 10.7885C4.96234 10.8805 4.99077 10.975 5.05107 11.0414L5.60286 11.6484H4.89985C4.80277 11.6484 4.72407 11.7271 4.72407 11.8242C4.72407 11.9213 4.80277 12 4.89985 12H9.59591C9.84329 12 10.0445 11.7987 10.0445 11.5514V9.89869C10.0445 9.80159 9.9658 9.72288 9.86872 9.72288ZM7.39187 6.16568L6.87509 7.20138C6.86225 7.19227 6.58838 6.99769 6.2536 6.75996C6.39887 6.58064 6.22892 6.79041 6.89192 5.97206L7.39187 6.16568ZM6.35284 7.26171L6.21882 7.46344H5.78047L5.64645 7.26171L5.99966 7.01086L6.35284 7.26171ZM5.33799 5.69815V5.36918C5.55155 5.45527 5.77979 5.49518 6.00268 5.49518C6.23314 5.49518 6.45915 5.44605 6.66015 5.35816V5.69958L5.99966 6.51485L5.33799 5.69815ZM7.98966 3.31732C7.98964 3.43247 7.93977 3.54143 7.85281 3.61622C7.79532 3.66567 7.72588 3.69729 7.64982 3.70966V2.92505C7.85185 2.95786 7.98966 3.12544 7.98966 3.31732ZM4.31293 3.70966C4.1086 3.6765 3.97309 3.50756 3.97309 3.3173C3.97309 3.12937 4.12888 2.97642 4.31293 2.93388V3.70966ZM4.32238 2.57569C4.26638 2.58211 4.20221 2.59727 4.15438 2.61354V2.17859C4.15438 1.17117 4.97394 0.351586 5.98135 0.351586C6.98877 0.351586 7.80833 1.17117 7.80833 2.17859V2.60147C7.75724 2.58593 7.70424 2.57566 7.64984 2.57084C7.64984 2.40122 7.65132 2.43394 7.09644 1.83738C6.99439 1.72774 6.82559 1.70477 6.69713 1.7887C6.08471 2.18876 5.33764 2.38973 4.64834 2.34023C4.49715 2.32891 4.35943 2.4285 4.32238 2.57569ZM4.66452 3.84806V2.75137V2.6936C5.4233 2.73804 6.21884 2.51262 6.86152 2.10103C7.11601 2.37427 7.25382 2.51759 7.29826 2.5721C7.29826 2.71001 7.29826 3.69019 7.29826 3.84804C7.29826 4.56164 6.7164 5.14357 6.0027 5.14357C5.20128 5.14359 4.66452 4.52189 4.66452 3.84806ZM5.12424 7.20138L4.60747 6.16568L5.10652 5.97239L5.74498 6.76048C5.42031 6.99103 5.1537 7.18045 5.12424 7.20138ZM5.32592 10.8211L5.83473 7.81498H6.16452L6.67332 10.8211L5.99963 11.5623L5.32592 10.8211Z" fill="currentColor"/></svg>
					<?php
		$employment_preference = get_user_meta( $user->ID, 'your_employment_preference', true );
		if ( ! empty( $employment_preference ) && is_array( $employment_preference ) ) {
			echo esc_html( implode( ', ', $employment_preference ) );
		}
					?>
				</span>
			</div>
		</div>
		<div class="employee-card-footer">
			<div class="experience-details">
				<div class="single-info">
					<p>Industry</p>
					<p class="industry">
						<?php
		$categories = get_user_meta( $user->ID, 'your_industry_categories', true );
		if ( ! empty( $categories ) && is_array( $categories ) ) {
			echo esc_html( implode( ', ', $categories ) );
		}
						?>
					</p>
				</div>
				<div class="single-info">
					<p>Qualifications:</p>
					<div class="qualification-list">
						<?php
		$qualifications = get_user_meta( $user->ID, 'your_qualifications', true );
		if ( !empty( $qualifications ) && is_array($qualifications) ) {
			foreach($qualifications as $qualification) {
				echo '<span>' . $qualification . '</span>';
			}
		}
						?>
					</div>
				</div>
				<div class="single-info">
					<p>Travel radius</p>
					<p><?php echo get_user_meta( $user->ID, 'your_travel_distance', true ) ?: 'Not set'; ?></p>
				</div>
			</div>
			<div class="employee-buttons">
				<a href="<?php echo get_author_posts_url( $user->ID )?: '#'; ?>" class="view-profile-btn">View Profile</a>
				<a href="#" class="message-btn">Message</a>
			</div>
		</div>
	</div>
	<?php endforeach; else : ?>
	<div class="empty-candidate-container">
		<p>No candidate profiles found.</p>
	</div>
	<?php
    endif;
    $html = ob_get_clean();
    wp_send_json_success(array(
		'html' => $html,
		'more_page' => $more_page,
		'page' => $page + 1
	));
}

// Candidate Dashboard
function candidates_dashboard(){
	ob_start();
?>
<div class="cdash_layout">
	<aside class="cdash_sidebar">
		<div class="logo">
			<a href="<?php echo esc_url(site_url()); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo-black.webp" alt="logo_black"></a>
		</div>
		<ul class="cdash_menu">
			<li><a href="#" class="active"><svg width="32" height="32" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M14.6663 5.33334V10.6667C14.6663 11.7275 14.2449 12.745 13.4948 13.4951C12.7446 14.2452 11.7272 14.6667 10.6663 14.6667H5.33301C4.27214 14.6667 3.25473 14.2452 2.50458 13.4951C1.75444 12.745 1.33301 11.7275 1.33301 10.6667V5.33334C1.33301 4.27247 1.75444 3.25505 2.50458 2.50491C3.25473 1.75476 4.27214 1.33334 5.33301 1.33334H10.6663C11.7272 1.33334 12.7446 1.75476 13.4948 2.50491C14.2449 3.25505 14.6663 4.27247 14.6663 5.33334ZM26.6663 1.33334H21.333C20.2721 1.33334 19.2547 1.75476 18.5046 2.50491C17.7544 3.25505 17.333 4.27247 17.333 5.33334V10.6667C17.333 11.7275 17.7544 12.745 18.5046 13.4951C19.2547 14.2452 20.2721 14.6667 21.333 14.6667H26.6663C27.7272 14.6667 28.7446 14.2452 29.4948 13.4951C30.2449 12.745 30.6663 11.7275 30.6663 10.6667V5.33334C30.6663 4.27247 30.2449 3.25505 29.4948 2.50491C28.7446 1.75476 27.7272 1.33334 26.6663 1.33334ZM10.6663 17.3333H5.33301C4.27214 17.3333 3.25473 17.7548 2.50458 18.5049C1.75444 19.2551 1.33301 20.2725 1.33301 21.3333V26.6667C1.33301 27.7275 1.75444 28.745 2.50458 29.4951C3.25473 30.2452 4.27214 30.6667 5.33301 30.6667H10.6663C11.7272 30.6667 12.7446 30.2452 13.4948 29.4951C14.2449 28.745 14.6663 27.7275 14.6663 26.6667V21.3333C14.6663 20.2725 14.2449 19.2551 13.4948 18.5049C12.7446 17.7548 11.7272 17.3333 10.6663 17.3333ZM26.6663 17.3333H21.333C20.2721 17.3333 19.2547 17.7548 18.5046 18.5049C17.7544 19.2551 17.333 20.2725 17.333 21.3333V26.6667C17.333 27.7275 17.7544 28.745 18.5046 29.4951C19.2547 30.2452 20.2721 30.6667 21.333 30.6667H26.6663C27.7272 30.6667 28.7446 30.2452 29.4948 29.4951C30.2449 28.745 30.6663 27.7275 30.6663 26.6667V21.3333C30.6663 20.2725 30.2449 19.2551 29.4948 18.5049C28.7446 17.7548 27.7272 17.3333 26.6663 17.3333Z" fill="currentColor"></svg> Dashboard</a></li>
			<li><a href="#"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M22.0615 3.40899C20.8047 2.15226 19.141 1.46538 17.3653 1.46538C15.5896 1.46538 13.9207 2.15735 12.664 3.41408L12.0076 4.07042L11.3411 3.4039C10.0844 2.14717 8.41044 1.45012 6.63474 1.45012C4.86413 1.45012 3.19527 2.14208 1.94363 3.39372C0.686904 4.65045 -0.00506011 6.31931 2.78603e-05 8.09501C2.78603e-05 9.87071 0.69708 11.5345 1.95381 12.7912L11.509 22.3464C11.6413 22.4787 11.8194 22.5499 11.9924 22.5499C12.1654 22.5499 12.3434 22.4838 12.4757 22.3515L22.0513 12.8116C23.308 11.5548 24 9.88597 24 8.11027C24.0051 6.33457 23.3182 4.66572 22.0615 3.40899ZM21.0846 11.8398L11.9924 20.8963L2.92052 11.8245C1.92328 10.8272 1.37378 9.50438 1.37378 8.09501C1.37378 6.68564 1.91819 5.36277 2.91543 4.37061C3.90759 3.37846 5.23046 2.82896 6.63474 2.82896C8.04411 2.82896 9.37207 3.37846 10.3693 4.3757L11.5192 5.52558C11.7889 5.79524 12.2213 5.79524 12.491 5.52558L13.6307 4.38588C14.6279 3.38864 15.9559 2.83913 17.3602 2.83913C18.7645 2.83913 20.0873 3.38864 21.0846 4.38079C22.0818 5.37803 22.6262 6.7009 22.6262 8.11027C22.6313 9.51964 22.0818 10.8425 21.0846 11.8398Z" fill="currentColor"></svg> Favorites</a></li>
			<li><a href="#"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M7.25 8C7.25 7.58579 7.58579 7.25 8 7.25H12C12.4142 7.25 12.75 7.58579 12.75 8C12.75 8.41421 12.4142 8.75 12 8.75H8C7.58579 8.75 7.25 8.41421 7.25 8Z" fill="currentColor"/><path d="M7.25 12C7.25 11.5858 7.58579 11.25 8 11.25H16C16.4142 11.25 16.75 11.5858 16.75 12C16.75 12.4142 16.4142 12.75 16 12.75H8C7.58579 12.75 7.25 12.4142 7.25 12Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M9.96644 1.25H14.0336C15.4053 1.25 16.4807 1.24999 17.3451 1.32061C18.2252 1.39252 18.9523 1.54138 19.6104 1.87671C20.6924 2.42798 21.572 3.30762 22.1233 4.38955C22.4586 5.04769 22.6075 5.77479 22.6794 6.65494C22.75 7.51928 22.75 8.59471 22.75 9.96642V11.1842V11.3311C22.7502 12.8797 22.7504 13.8244 22.5177 14.6179C21.9667 16.4971 20.4971 17.9667 18.6179 18.5177C17.8244 18.7504 16.8797 18.7502 15.3311 18.75C15.2827 18.75 15.2338 18.75 15.1842 18.75H14.6354L14.5751 18.7501C13.7079 18.7556 12.8632 19.0264 12.1543 19.5259L12.1051 19.5609L9.49441 21.4257C7.9899 22.5003 6.01288 20.9484 6.69954 19.2317C6.79183 19.001 6.62191 18.75 6.37341 18.75H5.77166C3.27441 18.75 1.25 16.7256 1.25 14.2283V9.96644C1.25 8.59472 1.24999 7.51929 1.32061 6.65494C1.39252 5.77479 1.54138 5.04769 1.87671 4.38955C2.42798 3.30762 3.30762 2.42798 4.38955 1.87671C5.04769 1.54138 5.77479 1.39252 6.65494 1.32061C7.51929 1.24999 8.59472 1.25 9.96644 1.25ZM6.77708 2.81563C5.9897 2.87996 5.48197 3.00359 5.07054 3.21322C4.27085 3.62068 3.62068 4.27085 3.21322 5.07054C3.00359 5.48197 2.87996 5.9897 2.81563 6.77708C2.75058 7.57322 2.75 8.58749 2.75 10V14.2283C2.75 15.8972 4.10284 17.25 5.77166 17.25H6.37341C7.68311 17.25 8.57867 18.5728 8.09226 19.7888C7.96197 20.1145 8.33709 20.409 8.62255 20.2051L11.2902 18.2997C12.2493 17.6239 13.3922 17.2576 14.5655 17.2501L14.6354 17.25H15.1842C16.9261 17.25 17.6363 17.2424 18.1958 17.0783C19.5848 16.671 20.671 15.5848 21.0783 14.1958C21.2424 13.6363 21.25 12.9261 21.25 11.1842V10C21.25 8.58749 21.2494 7.57322 21.1844 6.77708C21.12 5.9897 20.9964 5.48197 20.7868 5.07054C20.3793 4.27085 19.7291 3.62068 18.9295 3.21322C18.518 3.00359 18.0103 2.87996 17.2229 2.81563C16.4268 2.75058 15.4125 2.75 14 2.75H10C8.58749 2.75 7.57322 2.75058 6.77708 2.81563Z" fill="currentColor"/></svg>
 Messages</a></li>
			<li><a href="#"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M22.9461 13.9076L21.9476 13.0857C21.2637 12.5227 21.2649 11.4762 21.9477 10.9143L22.9461 10.0924C23.6565 9.50747 23.8383 8.50444 23.3782 7.70733L21.4067 4.29267C20.9465 3.49561 19.9867 3.15155 19.1251 3.47433L17.9141 3.92803C17.0846 4.23872 16.179 3.7147 16.0336 2.84236L15.821 1.5667C15.6697 0.658922 14.8919 0 13.9716 0H10.0287C9.10828 0 8.33044 0.658922 8.17917 1.56675L7.96655 2.84236C7.82091 3.71625 6.91406 4.2382 6.08611 3.92808L4.87509 3.47433C4.01339 3.15155 3.05376 3.49566 2.5935 4.29267L0.622077 7.70728C0.161905 8.5043 0.343593 9.50737 1.05417 10.0924L2.05256 10.9143C2.73656 11.4773 2.7352 12.5237 2.05256 13.0856L1.05412 13.9075C0.343593 14.4925 0.161858 15.4956 0.62203 16.2926L2.5935 19.7073C3.05372 20.5043 4.01316 20.8484 4.87509 20.5256L6.08606 20.0719C6.9157 19.7611 7.82114 20.2855 7.9665 21.1576L8.17912 22.4332C8.33044 23.3411 9.10828 24 10.0286 24H13.9715C14.8919 24 15.6697 23.3411 15.821 22.4333L16.0335 21.1577C16.1791 20.284 17.0859 19.7617 17.914 20.072L19.1251 20.5257C19.987 20.8485 20.9465 20.5044 21.4067 19.7073L23.3782 16.2926C23.8383 15.4956 23.6565 14.4925 22.9461 13.9076ZM19.7829 18.7698L18.5719 18.3161C16.636 17.5909 14.5233 18.8144 14.1841 20.8494L13.9716 22.125H10.0287L9.81605 20.8494C9.47625 18.8105 7.36045 17.5923 5.42831 18.3161L4.2173 18.7698L2.24587 15.3552L3.24426 14.5333C4.84031 13.2194 4.83698 10.7779 3.24426 9.46673L2.24587 8.64483L4.21734 5.23017L5.42831 5.68388C7.36425 6.40903 9.47691 5.18559 9.81605 3.15061L10.0286 1.875H13.9715L14.1841 3.15061C14.5239 5.18977 16.6398 6.40753 18.5718 5.68388L19.7828 5.23017L21.7547 8.64436C21.7547 8.64436 21.7546 8.6445 21.7542 8.64478L20.7559 9.46669C19.1599 10.7805 19.1631 13.222 20.7558 14.5332L21.7543 15.3551L19.7829 18.7698ZM12.0001 7.37498C9.44986 7.37498 7.37508 9.44977 7.37508 12C7.37508 14.5502 9.44986 16.625 12.0001 16.625C14.5503 16.625 16.6251 14.5502 16.6251 12C16.6251 9.44977 14.5503 7.37498 12.0001 7.37498ZM12.0001 14.75C10.4837 14.75 9.25008 13.5164 9.25008 12C9.25008 10.4836 10.4837 9.24998 12.0001 9.24998C13.5165 9.24998 14.7501 10.4836 14.7501 12C14.7501 13.5164 13.5165 14.75 12.0001 14.75Z" fill="currentColor"/></svg>
 Setting</a></li>
			<li><a href="#"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M16.3939 15.1301C15.2136 13.7008 13.6036 12.6907 11.8032 12.2497C12.7017 11.789 13.4191 11.0394 13.84 10.1215C14.2609 9.20366 14.3609 8.17091 14.1239 7.18935C13.8869 6.20779 13.3266 5.33447 12.5332 4.70985C11.7398 4.08523 10.7594 3.74561 9.74965 3.74561C8.73988 3.74561 7.75945 4.08523 6.96605 4.70985C6.17265 5.33447 5.61239 6.20779 5.37537 7.18935C5.13835 8.17091 5.23836 9.20366 5.6593 10.1215C6.08024 11.0394 6.79765 11.789 7.69615 12.2497C5.58011 12.7674 3.73885 14.0677 2.54328 15.8887C2.49722 15.9587 2.47589 16.042 2.48268 16.1255C2.48948 16.2089 2.52401 16.2877 2.58078 16.3492C3.8741 17.7591 5.55455 18.7561 7.41178 19.2155C9.269 19.6749 11.2205 19.5762 13.0219 18.9319C13.1153 18.8981 13.1914 18.8288 13.2337 18.739C13.276 18.6492 13.281 18.5463 13.2475 18.4528C13.2141 18.3593 13.145 18.283 13.0553 18.2404C12.9656 18.1978 12.8627 18.1926 12.7691 18.2257C11.1542 18.8033 9.40762 18.9059 7.73619 18.5214C6.06475 18.137 4.53852 17.2815 3.33827 16.0564C4.03818 15.0729 4.95499 14.2636 6.01775 13.6911C7.08052 13.1186 8.26074 12.7984 9.4671 12.7551C10.6735 12.7118 11.8736 12.9467 12.9747 13.4415C14.0757 13.9363 15.0482 14.6778 15.8168 15.6086C15.8802 15.6853 15.9715 15.7335 16.0706 15.7429C16.1696 15.7522 16.2683 15.7218 16.345 15.6583C16.4216 15.5949 16.4699 15.5036 16.4792 15.4045C16.4885 15.3055 16.4581 15.2068 16.3946 15.1301H16.3939ZM6.00077 8.25C6.00077 7.50832 6.22071 6.78329 6.63276 6.16661C7.04482 5.54992 7.63049 5.06928 8.31571 4.78545C9.00094 4.50162 9.75494 4.42736 10.4824 4.57205C11.2098 4.71675 11.878 5.0739 12.4024 5.59835C12.9269 6.12279 13.284 6.79098 13.4287 7.51841C13.5734 8.24584 13.4992 8.99984 13.2153 9.68506C12.9315 10.3703 12.4508 10.956 11.8342 11.368C11.2175 11.7801 10.4925 12 9.75078 12C8.75655 11.9989 7.80336 11.6035 7.10033 10.9004C6.39731 10.1974 6.00187 9.24422 6.00077 8.25Z" fill="currentColor"/><path d="M2.12063 15.5625C2.06086 15.5626 2.00193 15.5483 1.94876 15.521C1.8956 15.4937 1.84974 15.454 1.81501 15.4054C0.92422 14.1557 0.341089 12.7133 0.113073 11.1957C-0.114942 9.67801 0.0186024 8.12797 0.502838 6.67169C0.987073 5.21541 1.80832 3.89404 2.89973 2.81512C3.99114 1.7362 5.32188 0.930229 6.78364 0.462791C8.2454 -0.00464585 9.79687 -0.120336 11.3118 0.125137C12.8267 0.370609 14.2623 0.970308 15.5016 1.87542C16.741 2.78054 17.7491 3.9655 18.444 5.33386C19.1388 6.70222 19.5008 8.21531 19.5004 9.74999C19.4993 10.868 19.3046 11.9774 18.9248 13.029C18.89 13.121 18.8204 13.1957 18.7311 13.2369C18.6418 13.2781 18.5398 13.2826 18.4472 13.2493C18.3546 13.2161 18.2788 13.1477 18.2361 13.0591C18.1934 12.9705 18.1873 12.8686 18.219 12.7755C18.6607 11.5403 18.827 10.2234 18.7061 8.91721C18.5853 7.61099 18.1803 6.34699 17.5194 5.21379C16.8586 4.08059 15.958 3.1056 14.8806 2.35715C13.8033 1.6087 12.5753 1.10489 11.2828 0.881019C9.99021 0.657153 8.66433 0.718646 7.39804 1.06119C6.13175 1.40374 4.95568 2.01905 3.95225 2.864C2.94882 3.70896 2.14229 4.76312 1.5892 5.95262C1.0361 7.14212 0.749808 8.43819 0.750384 9.74999C0.747426 11.6222 1.33331 13.448 2.42513 14.9689C2.46533 15.0249 2.48931 15.0909 2.49442 15.1597C2.49954 15.2284 2.48559 15.2973 2.45412 15.3586C2.42265 15.42 2.37487 15.4715 2.31603 15.5075C2.2572 15.5434 2.18959 15.5625 2.12063 15.5625Z" fill="currentColor"/><path d="M23.6709 13.4546L22.1709 11.9546C21.9565 11.7501 21.6716 11.6359 21.3753 11.6359C21.079 11.6359 20.7941 11.7501 20.5797 11.9546L12.7047 19.8296C12.5504 19.9824 12.4445 20.1773 12.4002 20.3899L11.6356 23.5369C11.6204 23.5994 11.6216 23.6648 11.6389 23.7267C11.6563 23.7887 11.6893 23.8451 11.7348 23.8906C11.7803 23.9361 11.8368 23.9692 11.8987 23.9865C11.9607 24.0039 12.0261 24.0051 12.0886 23.9899L15.2202 23.2286C15.4383 23.1854 15.6386 23.0783 15.7955 22.9207L23.6705 15.0457C23.775 14.9413 23.8579 14.8172 23.9144 14.6807C23.971 14.5442 24.0001 14.3979 24.0001 14.2502C24.0001 14.1024 23.971 13.9561 23.9144 13.8196C23.8579 13.6831 23.7754 13.5591 23.6709 13.4546ZM23.1406 14.5151L15.2656 22.3901C15.2089 22.4452 15.1369 22.482 15.059 22.4955L12.509 23.115L12.9391 21.3446L13.6092 22.0147C13.68 22.083 13.7747 22.1208 13.873 22.12C13.9713 22.1191 14.0654 22.0797 14.1349 22.0102C14.2045 21.9406 14.2439 21.8466 14.2447 21.7483C14.2456 21.6499 14.2078 21.5552 14.1395 21.4845L13.1514 20.4964C13.1687 20.4453 13.197 20.3986 13.2342 20.3595L19.3452 14.25L20.7354 15.6401C20.8061 15.7084 20.9008 15.7462 20.9991 15.7454C21.0975 15.7445 21.1915 15.7051 21.2611 15.6355C21.3306 15.566 21.37 15.472 21.3709 15.3736C21.3717 15.2753 21.3339 15.1806 21.2656 15.1099L19.8755 13.7197L21.1104 12.4845C21.1807 12.4142 21.2761 12.3747 21.3755 12.3747C21.4749 12.3747 21.5703 12.4142 21.6406 12.4845L23.1406 13.9845C23.2109 14.0548 23.2504 14.1502 23.2504 14.2496C23.2504 14.349 23.2109 14.4448 23.1406 14.5151Z" fill="currentColor"/></svg> Edit Profile</a></li>
			<li><a href="#"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M22.3148 10.4636C22.223 7.84575 21.1628 5.39775 19.3013 3.53625C17.3502 1.58475 14.7571 0.510376 12.0001 0.510376C6.43171 0.510376 1.88184 4.9425 1.68571 10.4636C0.835961 10.8413 0.241211 11.6917 0.241211 12.6802V15.4035C0.241211 16.7407 1.32909 17.829 2.66671 17.829C3.53221 17.829 4.23609 17.1251 4.23609 16.2596V11.8238C4.23609 11.0111 3.61284 10.3485 2.82046 10.2697C3.11296 5.45963 7.11721 1.63538 12.0001 1.63538C14.4571 1.63538 16.7675 2.59313 18.5063 4.332C20.1031 5.92875 21.0376 8.01 21.1782 10.2701C20.3866 10.3496 19.7645 11.0119 19.7645 11.8238V16.2593C19.7645 17.0786 20.3975 17.7454 21.1992 17.8148V18.9356C21.1992 20.1979 20.1721 21.2246 18.9098 21.2246H17.2145C17.1312 20.9869 17.0007 20.7671 16.8177 20.5849C16.4971 20.2628 16.0696 20.0854 15.6143 20.0854H13.8668C13.616 20.0854 13.3752 20.1386 13.1547 20.2414C12.5532 20.5177 12.1647 21.1245 12.1647 21.7871C12.1647 22.2424 12.3421 22.6699 12.6635 22.9897C12.9848 23.3119 13.4123 23.4893 13.8668 23.4893H15.6143C16.3388 23.4893 16.9775 23.0209 17.2156 22.3496H18.9098C20.7927 22.3496 22.3242 20.8181 22.3242 18.9356V17.6145C23.1687 17.2346 23.759 16.3871 23.759 15.4028V12.6795C23.759 11.6918 23.1642 10.8413 22.3148 10.4636ZM3.11071 11.8238V16.2593C3.11071 16.5041 2.91159 16.7036 2.66634 16.7036C1.94934 16.7036 1.36584 16.1201 1.36584 15.4031V12.6799C1.36584 11.9625 1.94934 11.3794 2.66634 11.3794C2.91159 11.3794 3.11071 11.5789 3.11071 11.8238ZM16.1787 21.9079C16.1228 22.1726 15.8851 22.365 15.614 22.365H13.8665C13.7127 22.365 13.5683 22.305 13.4581 22.1947C13.3493 22.0864 13.2893 21.9416 13.2893 21.7879C13.2893 21.5632 13.421 21.3581 13.6272 21.2632C13.7007 21.2287 13.7813 21.2111 13.8665 21.2111H15.614C15.7677 21.2111 15.9121 21.2707 16.022 21.381C16.1307 21.4894 16.1907 21.6341 16.1907 21.7879C16.1911 21.8291 16.1866 21.8704 16.1787 21.9079ZM22.634 15.4031C22.634 16.1201 22.0505 16.7036 21.3335 16.7036C21.0886 16.7036 20.8891 16.5045 20.8891 16.2593V11.8238C20.8891 11.5789 21.0882 11.3794 21.3335 11.3794C22.0505 11.3794 22.634 11.9629 22.634 12.6799V15.4031Z" fill="currentColor"/><path d="M15.6429 15.597C16.9572 15.597 18.0264 14.5275 18.0264 13.2135V8.35689C18.0264 7.72164 17.7781 7.12314 17.3274 6.67239C16.8766 6.22164 16.2785 5.97339 15.6429 5.97339H8.35811C7.04373 5.97339 5.97461 7.04251 5.97461 8.35689V13.2135C5.97461 14.5279 7.04373 15.597 8.35811 15.597H8.40273V16.8555C8.40273 17.3355 8.68886 17.7615 9.13136 17.9411C9.27423 17.9985 9.42273 18.027 9.56973 18.027C9.87611 18.027 10.1746 17.9055 10.3932 17.6794L12.4869 15.597H15.6429ZM11.8587 14.6355L9.59298 16.8889C9.58286 16.8994 9.57536 16.9073 9.55398 16.8979C9.52811 16.8874 9.52811 16.8705 9.52811 16.8555V15.0345C9.52811 14.724 9.27648 14.472 8.96561 14.472H8.35848C7.66436 14.472 7.09998 13.9073 7.09998 13.2135V8.35689C7.09998 7.66276 7.66436 7.09839 8.35848 7.09839H15.6432C15.9785 7.09839 16.2939 7.22964 16.5324 7.46776C16.7709 7.70626 16.9017 8.02201 16.9017 8.35689V13.2135C16.9017 13.9076 16.337 14.472 15.6432 14.472H12.2555C12.1066 14.472 11.9641 14.5309 11.8587 14.6355Z" fill="currentColor"/><path d="M9.26737 10.0451C8.80987 10.0451 8.4375 10.4179 8.4375 10.875C8.4375 11.3321 8.81025 11.7049 9.26737 11.7049C9.72525 11.7049 10.098 11.3321 10.098 10.875C10.098 10.4179 9.72562 10.0451 9.26737 10.0451Z" fill="currentColor"/><path d="M11.9998 10.0451C11.5423 10.0451 11.1699 10.4179 11.1699 10.875C11.1699 11.3321 11.5427 11.7049 11.9998 11.7049C12.458 11.7049 12.8304 11.3321 12.8304 10.875C12.8304 10.4179 12.458 10.0451 11.9998 10.0451Z" fill="currentColor"/><path d="M14.7322 10.0451C14.2747 10.0451 13.9023 10.4179 13.9023 10.875C13.9023 11.3321 14.2751 11.7049 14.7322 11.7049C15.1901 11.7049 15.5628 11.3321 15.5628 10.875C15.5628 10.4179 15.1901 10.0451 14.7322 10.0451Z" fill="currentColor"/></svg> Support</a></li>
			<li><a href="#"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22.9335 18.4587L19.1607 10.4029C19.0784 10.2271 18.869 10.1514 18.6931 10.2337C18.5173 10.316 18.4415 10.5254 18.5239 10.7012L22.2967 18.7567C22.4654 19.1176 22.3092 19.5488 21.9485 19.7178L14.7149 23.1054C14.3542 23.2744 13.9232 23.1183 13.7541 22.7576L12.4017 19.8691L15.9217 11.3762C16.0738 11.0091 16.0653 10.5919 15.8984 10.2314L13.973 6.07225L16.4688 6.86182C16.6608 6.92247 16.8198 7.06179 16.9051 7.24441L17.8296 9.21758C17.8894 9.34518 18.016 9.42008 18.1482 9.42008C18.1981 9.42008 18.2489 9.40935 18.2971 9.3868C18.473 9.30444 18.5487 9.09505 18.4663 8.91922L17.542 6.94638C17.3738 6.58629 17.0598 6.31108 16.6806 6.19136L13.6708 5.23914C14.2913 4.33266 14.945 3.12051 15.1699 2.57784C15.7597 1.15489 15.2837 0.363546 14.6416 0.0973879C13.9994 -0.168676 13.1032 0.0539344 12.5134 1.47688C12.2598 2.08888 12.027 3.42174 11.8615 4.56516C11.6646 4.5774 11.4666 4.61865 11.2737 4.69238L6.71992 6.4323C6.35561 6.57152 6.061 6.85802 5.91165 7.21826L1.04108 18.9701C0.895248 19.322 0.895154 19.7095 1.04084 20.0614C1.18648 20.4133 1.46047 20.6874 1.81227 20.8331L3.23681 21.4235C3.4162 21.4979 3.62194 21.4127 3.69623 21.2334C3.77058 21.054 3.68545 20.8483 3.50606 20.7739L2.08152 20.1835C1.90325 20.1096 1.76436 19.9707 1.69053 19.7924C1.61671 19.614 1.61675 19.4176 1.69067 19.2393L6.56139 7.48732C6.63709 7.30474 6.78639 7.15952 6.97103 7.08898L11.5248 5.34906C11.6031 5.31915 11.6826 5.2975 11.7625 5.28339C11.7191 5.61362 11.6838 5.90242 11.6583 6.11889C11.5092 6.12742 11.3615 6.16422 11.2222 6.22947C10.9348 6.364 10.7171 6.60241 10.6091 6.90072C10.501 7.19909 10.5156 7.52159 10.6502 7.80893C10.8519 8.23972 11.2808 8.49294 11.7277 8.49294C11.8962 8.49294 12.0673 8.45694 12.2296 8.38091C12.517 8.24637 12.7347 8.00792 12.8428 7.70965C12.9508 7.41129 12.9362 7.08879 12.8017 6.80144C12.7014 6.5875 12.5436 6.41228 12.3463 6.29205C12.3725 6.06817 12.4124 5.73803 12.463 5.3537C12.7673 5.47229 13.0275 5.70344 13.1737 6.01942L15.2602 10.5266C15.3448 10.7093 15.3491 10.9208 15.272 11.1068L10.4046 22.8509C10.3307 23.0292 10.1918 23.1681 10.0134 23.2419C9.83511 23.3158 9.6387 23.3157 9.46039 23.2418L5.11041 21.439C4.93112 21.3645 4.72533 21.4497 4.65099 21.6291C4.57669 21.8085 4.66177 22.0142 4.84116 22.0885L9.19114 23.8914C9.36903 23.9652 9.55358 24 9.73517 24C10.2948 24 10.8269 23.6685 11.0541 23.1201L12.037 20.7487L13.1172 23.0558C13.3594 23.5727 13.8743 23.8767 14.4106 23.8767C14.6128 23.8767 14.8182 23.8334 15.0131 23.7421L22.2467 20.3545C22.9584 20.0211 23.2665 19.1707 22.9335 18.4587ZM12.1816 7.47021C12.1375 7.5919 12.0487 7.68917 11.9314 7.7441C11.6895 7.85745 11.4003 7.75273 11.287 7.51076C11.2321 7.39357 11.2261 7.26195 11.2702 7.14021C11.3143 7.01852 11.4031 6.92126 11.5203 6.86632C11.5857 6.83571 11.6555 6.82033 11.7255 6.82033C11.7812 6.82033 11.837 6.83004 11.8908 6.84954C12.0125 6.8936 12.1098 6.98243 12.1647 7.09966C12.2197 7.21695 12.2257 7.34848 12.1816 7.47021ZM13.0675 4.87581C12.9099 4.77592 12.7392 4.69834 12.5607 4.64513C12.7193 3.54792 12.9367 2.29265 13.1632 1.74604C13.3227 1.36105 13.7599 0.493203 14.3725 0.746939C14.9849 1.00082 14.68 1.9236 14.5204 2.30854C14.3042 2.83017 13.6667 4.00899 13.0675 4.87581Z" fill="currentColor"/><path d="M7.23809 11.8544C7.99982 11.449 8.87385 11.3644 9.69923 11.6164C11.0692 12.0347 11.9888 13.277 11.9874 14.708C11.9873 14.9021 12.1445 15.0597 12.3387 15.0599C12.3388 15.0599 12.3389 15.0599 12.3391 15.0599C12.5331 15.0599 12.6905 14.9027 12.6907 14.7086C12.6914 13.883 12.4229 13.059 11.9345 12.3883C11.4294 11.6946 10.7274 11.1951 9.9045 10.9438C7.82985 10.3105 5.62685 11.4831 4.99361 13.5576C4.68681 14.5626 4.78975 15.6269 5.28349 16.5545C5.77718 17.482 6.60246 18.1618 7.60742 18.4686C7.98303 18.5833 8.37018 18.6402 8.75662 18.6402C9.30614 18.6402 9.85383 18.525 10.363 18.2969C11.2216 17.9122 11.9142 17.2339 12.3132 16.3869C12.396 16.2113 12.3206 16.0018 12.145 15.919C11.9694 15.8362 11.7598 15.9116 11.6771 16.0872C10.9989 17.5269 9.33773 18.2616 7.81283 17.796C6.9875 17.5441 6.30973 16.9858 5.90426 16.2241C5.49883 15.4624 5.41427 14.5883 5.66627 13.7629C5.91808 12.9377 6.47642 12.2598 7.23809 11.8544Z" fill="currentColor"/><path d="M10.1087 13.7493C10.0246 13.5911 9.88386 13.4752 9.71244 13.4229L9.55273 13.3741L9.66125 13.0186C9.71792 12.8329 9.61334 12.6364 9.42767 12.5797C9.24186 12.5231 9.04545 12.6276 8.98873 12.8133L8.87778 13.1768C8.40935 13.096 7.94154 13.3742 7.79927 13.8405C7.72455 14.0852 7.74963 14.3444 7.86986 14.5703C7.99005 14.7961 8.1911 14.9617 8.43579 15.0364L8.87839 15.1715C8.9435 15.1914 8.99693 15.2354 9.0289 15.2955C9.06087 15.3555 9.06753 15.4245 9.04765 15.4896C9.02778 15.5546 8.98376 15.6081 8.92367 15.64C8.86362 15.6721 8.79471 15.6787 8.7296 15.6588L8.50835 15.5912L8.04194 15.4489C8.07855 15.273 7.97552 15.0949 7.80026 15.0414C7.61444 14.9848 7.41799 15.0893 7.36132 15.275C7.25336 15.6288 7.45324 16.0044 7.807 16.1124L7.96676 16.1612L7.85824 16.5167C7.80157 16.7024 7.90615 16.8989 8.09182 16.9556C8.12604 16.9661 8.16059 16.971 8.19457 16.971C8.34513 16.971 8.48449 16.8735 8.53076 16.722L8.64162 16.3588C8.69599 16.3683 8.75065 16.3736 8.80521 16.3736C8.96004 16.3736 9.11356 16.3356 9.25409 16.2608C9.47994 16.1406 9.64555 15.9395 9.72022 15.6948C9.87444 15.1896 9.58892 14.6531 9.0837 14.4989L8.6411 14.3638C8.57604 14.3439 8.5226 14.2999 8.49063 14.2398C8.45867 14.1798 8.45196 14.1108 8.47188 14.0457C8.51285 13.9114 8.65545 13.8353 8.78998 13.8765L9.01109 13.944L9.01123 13.944H9.01132L9.47759 14.0864C9.44098 14.2622 9.54402 14.4403 9.71928 14.4938C9.905 14.5505 10.1016 14.4459 10.1582 14.2602C10.2105 14.0889 10.1929 13.9074 10.1087 13.7493Z" fill="currentColor"/><path d="M20.6055 18.4402L18.3553 13.6585C18.2726 13.4827 18.063 13.4073 17.8875 13.4901C17.7118 13.5727 17.6364 13.7822 17.7191 13.9579L19.9693 18.7396C20.0292 18.8669 20.1557 18.9416 20.2877 18.9416C20.3378 18.9416 20.3888 18.9308 20.4371 18.908C20.6128 18.8253 20.6882 18.6159 20.6055 18.4402Z" fill="currentColor"/><path d="M19.0333 19.2809L16.7831 14.4992C16.7004 14.3234 16.4908 14.248 16.3153 14.3307C16.1396 14.4134 16.0641 14.6229 16.1468 14.7986L18.397 19.5803C18.4569 19.7076 18.5834 19.7822 18.7154 19.7822C18.7656 19.7822 18.8165 19.7715 18.8648 19.7487C19.0405 19.666 19.1159 19.4565 19.0333 19.2809Z" fill="currentColor"/></svg> Plans</a></li>
		</ul>
	</aside>

	<main class="cdash_main">
		<div class="cdash_header">
			<div class="title">
				<h2>Welcome Back, Jane!</h2>
				<p>Join hospitality and retail employers</p>
			</div>
			<div class="profiles">
				<button class="company-profile">Company Profile</button>
				<div class="profiles-content">
					<button class="notification-btn" aria-label="Notifications"><svg width="32" height="32" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M17.9997 5.57333C17.6317 5.57333 17.333 5.27467 17.333 4.90667V2.66667C17.333 1.932 16.7343 1.33333 15.9997 1.33333C15.265 1.33333 14.6663 1.932 14.6663 2.66667V4.90667C14.6663 5.27467 14.3677 5.57333 13.9997 5.57333C13.6317 5.57333 13.333 5.276 13.333 4.90667V2.66667C13.333 1.196 14.529 0 15.9997 0C17.4703 0 18.6663 1.196 18.6663 2.66667V4.90667C18.6663 5.276 18.3677 5.57333 17.9997 5.57333Z" fill="currentColor"/><path d="M15.9997 32C13.4263 32 11.333 29.9067 11.333 27.3333C11.333 26.9653 11.6317 26.6667 11.9997 26.6667C12.3677 26.6667 12.6663 26.9653 12.6663 27.3333C12.6663 29.1707 14.1623 30.6667 15.9997 30.6667C17.837 30.6667 19.333 29.1707 19.333 27.3333C19.333 26.9653 19.6317 26.6667 19.9997 26.6667C20.3677 26.6667 20.6663 26.9653 20.6663 27.3333C20.6663 29.9067 18.573 32 15.9997 32Z" fill="currentColor"/><path d="M27.3337 28H4.66699C3.56433 28 2.66699 27.1027 2.66699 26C2.66699 25.4147 2.92166 24.8613 3.36699 24.48C5.46833 22.704 6.66699 20.12 6.66699 17.384V13.3333C6.66699 8.18667 10.8537 4 16.0003 4C21.147 4 25.3337 8.18667 25.3337 13.3333V17.384C25.3337 20.1213 26.5323 22.704 28.623 24.4707C29.079 24.8613 29.3337 25.4147 29.3337 26C29.3337 27.1027 28.4377 28 27.3337 28ZM16.0003 5.33333C11.5883 5.33333 8.00033 8.92133 8.00033 13.3333V17.384C8.00033 20.5147 6.62966 23.468 4.23899 25.4893C4.08566 25.62 4.00033 25.8053 4.00033 26C4.00033 26.368 4.29899 26.6667 4.66699 26.6667H27.3337C27.7017 26.6667 28.0003 26.368 28.0003 26C28.0003 25.8053 27.915 25.62 27.767 25.4933C25.3723 23.468 24.0003 20.5133 24.0003 17.384V13.3333C24.0003 8.92133 20.4123 5.33333 16.0003 5.33333Z" fill="currentColor"/></svg></button>
					<div class="profile-photo"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/employee-sample.webp" alt="profile_photo"></div>
				</div>
			</div>
		</div>
		<hr class="cdash-divider">
		<div class="banner">
			<h3>Track Your Profile Performance</h3>
			<p>Monitor employer engagement, profile visibility, and opportunities to improve your professional presence. Stay informed about profile views, employer interactions, resume downloads, and saved profile activity. Keep your information up to date, showcase your latest skills.</p>
		</div>

		<div class="section-header">
			<h3>Recent Employer Activity</h3>
			<button class="btn-view">View All</button>
		</div>

		<div class="cdash_table">
			<table>
				<thead>
					<tr>
						<th>Name</th>
						<th>Activity</th>
						<th>Date</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><div class="row-name"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/employee-sample.webp" alt="profile_photo"> Starbucks</div></td>
						<td>Viewed Profile</td>
						<td>
							<div class="date-cell">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M6.66732 12.0834C7.12756 12.0834 7.50065 11.7103 7.50065 11.25C7.50065 10.7898 7.12756 10.4167 6.66732 10.4167C6.20708 10.4167 5.83398 10.7898 5.83398 11.25C5.83398 11.7103 6.20708 12.0834 6.66732 12.0834Z" fill="currentColor"/><path d="M6.66732 15.4167C7.12756 15.4167 7.50065 15.0436 7.50065 14.5833C7.50065 14.1231 7.12756 13.75 6.66732 13.75C6.20708 13.75 5.83398 14.1231 5.83398 14.5833C5.83398 15.0436 6.20708 15.4167 6.66732 15.4167Z" fill="currentColor"/><path d="M10.0003 12.0834C10.4606 12.0834 10.8337 11.7103 10.8337 11.25C10.8337 10.7898 10.4606 10.4167 10.0003 10.4167C9.54009 10.4167 9.16699 10.7898 9.16699 11.25C9.16699 11.7103 9.54009 12.0834 10.0003 12.0834Z" fill="currentColor"/><path d="M10.0003 15.4167C10.4606 15.4167 10.8337 15.0436 10.8337 14.5833C10.8337 14.1231 10.4606 13.75 10.0003 13.75C9.54009 13.75 9.16699 14.1231 9.16699 14.5833C9.16699 15.0436 9.54009 15.4167 10.0003 15.4167Z" fill="currentColor"/><path d="M13.3333 12.0834C13.7936 12.0834 14.1667 11.7103 14.1667 11.25C14.1667 10.7898 13.7936 10.4167 13.3333 10.4167C12.8731 10.4167 12.5 10.7898 12.5 11.25C12.5 11.7103 12.8731 12.0834 13.3333 12.0834Z" fill="currentColor"/><path d="M13.3333 15.4167C13.7936 15.4167 14.1667 15.0436 14.1667 14.5833C14.1667 14.1231 13.7936 13.75 13.3333 13.75C12.8731 13.75 12.5 14.1231 12.5 14.5833C12.5 15.0436 12.8731 15.4167 13.3333 15.4167Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.9587 5.41669V16.25C18.9587 17.0792 18.6295 17.8734 18.0437 18.46C17.457 19.0459 16.6628 19.375 15.8337 19.375H4.16699C3.33783 19.375 2.54366 19.0459 1.95699 18.46C1.37116 17.8734 1.04199 17.0792 1.04199 16.25V5.41669C1.04199 4.58752 1.37116 3.79335 1.95699 3.20669C2.54366 2.62085 3.33783 2.29169 4.16699 2.29169H15.8337C16.6628 2.29169 17.457 2.62085 18.0437 3.20669C18.6295 3.79335 18.9587 4.58752 18.9587 5.41669ZM17.7087 5.41669C17.7087 4.91919 17.5112 4.44252 17.1595 4.09085C16.8078 3.73919 16.3312 3.54169 15.8337 3.54169H4.16699C3.66949 3.54169 3.19283 3.73919 2.84116 4.09085C2.48949 4.44252 2.29199 4.91919 2.29199 5.41669V16.25C2.29199 16.7475 2.48949 17.2242 2.84116 17.5759C3.19283 17.9275 3.66949 18.125 4.16699 18.125H15.8337C16.3312 18.125 16.8078 17.9275 17.1595 17.5759C17.5112 17.2242 17.7087 16.7475 17.7087 16.25V5.41669Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.3337 6.875C18.6787 6.875 18.9587 7.155 18.9587 7.5C18.9587 7.845 18.6787 8.125 18.3337 8.125H1.66699C1.32199 8.125 1.04199 7.845 1.04199 7.5C1.04199 7.155 1.32199 6.875 1.66699 6.875H18.3337Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.709 1.25C12.709 0.905 12.989 0.625 13.334 0.625C13.679 0.625 13.959 0.905 13.959 1.25V4.58333C13.959 4.92833 13.679 5.20833 13.334 5.20833C12.989 5.20833 12.709 4.92833 12.709 4.58333V1.25Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04199 1.25C6.04199 0.905 6.32199 0.625 6.66699 0.625C7.01199 0.625 7.29199 0.905 7.29199 1.25V4.58333C7.29199 4.92833 7.01199 5.20833 6.66699 5.20833C6.32199 5.20833 6.04199 4.92833 6.04199 4.58333V1.25Z" fill="currentColor"/></svg>
								23 Jan 2026
							</div>
						</td>
						<td><button class="action-btn" aria-label="More options"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 7.5C12.8284 7.5 13.5 6.82843 13.5 6C13.5 5.17157 12.8284 4.5 12 4.5C11.1716 4.5 10.5 5.17157 10.5 6C10.5 6.82843 11.1716 7.5 12 7.5Z" fill="currentColor"/><path d="M12 13.5C12.8284 13.5 13.5 12.8284 13.5 12C13.5 11.1716 12.8284 10.5 12 10.5C11.1716 10.5 10.5 11.1716 10.5 12C10.5 12.8284 11.1716 13.5 12 13.5Z" fill="currentColor"/><path d="M12 19C12.8284 19 13.5 18.3284 13.5 17.5C13.5 16.6716 12.8284 16 12 16C11.1716 16 10.5 16.6716 10.5 17.5C10.5 18.3284 11.1716 19 12 19Z" fill="currentColor"/></svg></button></td>
					</tr>
					<tr>
						<td><div class="row-name"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/employee-sample.webp" alt="profile_photo"> McDonald's</div></td>
						<td>Saved Candidate</td>
						<td>
							<div class="date-cell">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M6.66732 12.0834C7.12756 12.0834 7.50065 11.7103 7.50065 11.25C7.50065 10.7898 7.12756 10.4167 6.66732 10.4167C6.20708 10.4167 5.83398 10.7898 5.83398 11.25C5.83398 11.7103 6.20708 12.0834 6.66732 12.0834Z" fill="currentColor"/><path d="M6.66732 15.4167C7.12756 15.4167 7.50065 15.0436 7.50065 14.5833C7.50065 14.1231 7.12756 13.75 6.66732 13.75C6.20708 13.75 5.83398 14.1231 5.83398 14.5833C5.83398 15.0436 6.20708 15.4167 6.66732 15.4167Z" fill="currentColor"/><path d="M10.0003 12.0834C10.4606 12.0834 10.8337 11.7103 10.8337 11.25C10.8337 10.7898 10.4606 10.4167 10.0003 10.4167C9.54009 10.4167 9.16699 10.7898 9.16699 11.25C9.16699 11.7103 9.54009 12.0834 10.0003 12.0834Z" fill="currentColor"/><path d="M10.0003 15.4167C10.4606 15.4167 10.8337 15.0436 10.8337 14.5833C10.8337 14.1231 10.4606 13.75 10.0003 13.75C9.54009 13.75 9.16699 14.1231 9.16699 14.5833C9.16699 15.0436 9.54009 15.4167 10.0003 15.4167Z" fill="currentColor"/><path d="M13.3333 12.0834C13.7936 12.0834 14.1667 11.7103 14.1667 11.25C14.1667 10.7898 13.7936 10.4167 13.3333 10.4167C12.8731 10.4167 12.5 10.7898 12.5 11.25C12.5 11.7103 12.8731 12.0834 13.3333 12.0834Z" fill="currentColor"/><path d="M13.3333 15.4167C13.7936 15.4167 14.1667 15.0436 14.1667 14.5833C14.1667 14.1231 13.7936 13.75 13.3333 13.75C12.8731 13.75 12.5 14.1231 12.5 14.5833C12.5 15.0436 12.8731 15.4167 13.3333 15.4167Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.9587 5.41669V16.25C18.9587 17.0792 18.6295 17.8734 18.0437 18.46C17.457 19.0459 16.6628 19.375 15.8337 19.375H4.16699C3.33783 19.375 2.54366 19.0459 1.95699 18.46C1.37116 17.8734 1.04199 17.0792 1.04199 16.25V5.41669C1.04199 4.58752 1.37116 3.79335 1.95699 3.20669C2.54366 2.62085 3.33783 2.29169 4.16699 2.29169H15.8337C16.6628 2.29169 17.457 2.62085 18.0437 3.20669C18.6295 3.79335 18.9587 4.58752 18.9587 5.41669ZM17.7087 5.41669C17.7087 4.91919 17.5112 4.44252 17.1595 4.09085C16.8078 3.73919 16.3312 3.54169 15.8337 3.54169H4.16699C3.66949 3.54169 3.19283 3.73919 2.84116 4.09085C2.48949 4.44252 2.29199 4.91919 2.29199 5.41669V16.25C2.29199 16.7475 2.48949 17.2242 2.84116 17.5759C3.19283 17.9275 3.66949 18.125 4.16699 18.125H15.8337C16.3312 18.125 16.8078 17.9275 17.1595 17.5759C17.5112 17.2242 17.7087 16.7475 17.7087 16.25V5.41669Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.3337 6.875C18.6787 6.875 18.9587 7.155 18.9587 7.5C18.9587 7.845 18.6787 8.125 18.3337 8.125H1.66699C1.32199 8.125 1.04199 7.845 1.04199 7.5C1.04199 7.155 1.32199 6.875 1.66699 6.875H18.3337Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.709 1.25C12.709 0.905 12.989 0.625 13.334 0.625C13.679 0.625 13.959 0.905 13.959 1.25V4.58333C13.959 4.92833 13.679 5.20833 13.334 5.20833C12.989 5.20833 12.709 4.92833 12.709 4.58333V1.25Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04199 1.25C6.04199 0.905 6.32199 0.625 6.66699 0.625C7.01199 0.625 7.29199 0.905 7.29199 1.25V4.58333C7.29199 4.92833 7.01199 5.20833 6.66699 5.20833C6.32199 5.20833 6.04199 4.92833 6.04199 4.58333V1.25Z" fill="currentColor"/></svg>
								23 Jan 2026
							</div>
						</td>
						<td><button class="action-btn" aria-label="More options"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 7.5C12.8284 7.5 13.5 6.82843 13.5 6C13.5 5.17157 12.8284 4.5 12 4.5C11.1716 4.5 10.5 5.17157 10.5 6C10.5 6.82843 11.1716 7.5 12 7.5Z" fill="currentColor"/><path d="M12 13.5C12.8284 13.5 13.5 12.8284 13.5 12C13.5 11.1716 12.8284 10.5 12 10.5C11.1716 10.5 10.5 11.1716 10.5 12C10.5 12.8284 11.1716 13.5 12 13.5Z" fill="currentColor"/><path d="M12 19C12.8284 19 13.5 18.3284 13.5 17.5C13.5 16.6716 12.8284 16 12 16C11.1716 16 10.5 16.6716 10.5 17.5C10.5 18.3284 11.1716 19 12 19Z" fill="currentColor"/></svg></button></td>
					</tr>
					<tr>
						<td><div class="row-name"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/employee-sample.webp" alt="profile_photo"> Subway</div></td>
						<td>Downloaded Resume</td>
						<td>
							<div class="date-cell">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M6.66732 12.0834C7.12756 12.0834 7.50065 11.7103 7.50065 11.25C7.50065 10.7898 7.12756 10.4167 6.66732 10.4167C6.20708 10.4167 5.83398 10.7898 5.83398 11.25C5.83398 11.7103 6.20708 12.0834 6.66732 12.0834Z" fill="currentColor"/><path d="M6.66732 15.4167C7.12756 15.4167 7.50065 15.0436 7.50065 14.5833C7.50065 14.1231 7.12756 13.75 6.66732 13.75C6.20708 13.75 5.83398 14.1231 5.83398 14.5833C5.83398 15.0436 6.20708 15.4167 6.66732 15.4167Z" fill="currentColor"/><path d="M10.0003 12.0834C10.4606 12.0834 10.8337 11.7103 10.8337 11.25C10.8337 10.7898 10.4606 10.4167 10.0003 10.4167C9.54009 10.4167 9.16699 10.7898 9.16699 11.25C9.16699 11.7103 9.54009 12.0834 10.0003 12.0834Z" fill="currentColor"/><path d="M10.0003 15.4167C10.4606 15.4167 10.8337 15.0436 10.8337 14.5833C10.8337 14.1231 10.4606 13.75 10.0003 13.75C9.54009 13.75 9.16699 14.1231 9.16699 14.5833C9.16699 15.0436 9.54009 15.4167 10.0003 15.4167Z" fill="currentColor"/><path d="M13.3333 12.0834C13.7936 12.0834 14.1667 11.7103 14.1667 11.25C14.1667 10.7898 13.7936 10.4167 13.3333 10.4167C12.8731 10.4167 12.5 10.7898 12.5 11.25C12.5 11.7103 12.8731 12.0834 13.3333 12.0834Z" fill="currentColor"/><path d="M13.3333 15.4167C13.7936 15.4167 14.1667 15.0436 14.1667 14.5833C14.1667 14.1231 13.7936 13.75 13.3333 13.75C12.8731 13.75 12.5 14.1231 12.5 14.5833C12.5 15.0436 12.8731 15.4167 13.3333 15.4167Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.9587 5.41669V16.25C18.9587 17.0792 18.6295 17.8734 18.0437 18.46C17.457 19.0459 16.6628 19.375 15.8337 19.375H4.16699C3.33783 19.375 2.54366 19.0459 1.95699 18.46C1.37116 17.8734 1.04199 17.0792 1.04199 16.25V5.41669C1.04199 4.58752 1.37116 3.79335 1.95699 3.20669C2.54366 2.62085 3.33783 2.29169 4.16699 2.29169H15.8337C16.6628 2.29169 17.457 2.62085 18.0437 3.20669C18.6295 3.79335 18.9587 4.58752 18.9587 5.41669ZM17.7087 5.41669C17.7087 4.91919 17.5112 4.44252 17.1595 4.09085C16.8078 3.73919 16.3312 3.54169 15.8337 3.54169H4.16699C3.66949 3.54169 3.19283 3.73919 2.84116 4.09085C2.48949 4.44252 2.29199 4.91919 2.29199 5.41669V16.25C2.29199 16.7475 2.48949 17.2242 2.84116 17.5759C3.19283 17.9275 3.66949 18.125 4.16699 18.125H15.8337C16.3312 18.125 16.8078 17.9275 17.1595 17.5759C17.5112 17.2242 17.7087 16.7475 17.7087 16.25V5.41669Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.3337 6.875C18.6787 6.875 18.9587 7.155 18.9587 7.5C18.9587 7.845 18.6787 8.125 18.3337 8.125H1.66699C1.32199 8.125 1.04199 7.845 1.04199 7.5C1.04199 7.155 1.32199 6.875 1.66699 6.875H18.3337Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.709 1.25C12.709 0.905 12.989 0.625 13.334 0.625C13.679 0.625 13.959 0.905 13.959 1.25V4.58333C13.959 4.92833 13.679 5.20833 13.334 5.20833C12.989 5.20833 12.709 4.92833 12.709 4.58333V1.25Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04199 1.25C6.04199 0.905 6.32199 0.625 6.66699 0.625C7.01199 0.625 7.29199 0.905 7.29199 1.25V4.58333C7.29199 4.92833 7.01199 5.20833 6.66699 5.20833C6.32199 5.20833 6.04199 4.92833 6.04199 4.58333V1.25Z" fill="currentColor"/></svg>
								23 Jan 2026
							</div>
						</td>
						<td><button class="action-btn" aria-label="More options"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 7.5C12.8284 7.5 13.5 6.82843 13.5 6C13.5 5.17157 12.8284 4.5 12 4.5C11.1716 4.5 10.5 5.17157 10.5 6C10.5 6.82843 11.1716 7.5 12 7.5Z" fill="currentColor"/><path d="M12 13.5C12.8284 13.5 13.5 12.8284 13.5 12C13.5 11.1716 12.8284 10.5 12 10.5C11.1716 10.5 10.5 11.1716 10.5 12C10.5 12.8284 11.1716 13.5 12 13.5Z" fill="currentColor"/><path d="M12 19C12.8284 19 13.5 18.3284 13.5 17.5C13.5 16.6716 12.8284 16 12 16C11.1716 16 10.5 16.6716 10.5 17.5C10.5 18.3284 11.1716 19 12 19Z" fill="currentColor"/></svg></button></td>
					</tr>
					<tr>
						<td><div class="row-name"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/employee-sample.webp" alt="profile_photo"> Burger King</div></td>
						<td>Sent Message</td>
						<td>
							<div class="date-cell">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M6.66732 12.0834C7.12756 12.0834 7.50065 11.7103 7.50065 11.25C7.50065 10.7898 7.12756 10.4167 6.66732 10.4167C6.20708 10.4167 5.83398 10.7898 5.83398 11.25C5.83398 11.7103 6.20708 12.0834 6.66732 12.0834Z" fill="currentColor"/><path d="M6.66732 15.4167C7.12756 15.4167 7.50065 15.0436 7.50065 14.5833C7.50065 14.1231 7.12756 13.75 6.66732 13.75C6.20708 13.75 5.83398 14.1231 5.83398 14.5833C5.83398 15.0436 6.20708 15.4167 6.66732 15.4167Z" fill="currentColor"/><path d="M10.0003 12.0834C10.4606 12.0834 10.8337 11.7103 10.8337 11.25C10.8337 10.7898 10.4606 10.4167 10.0003 10.4167C9.54009 10.4167 9.16699 10.7898 9.16699 11.25C9.16699 11.7103 9.54009 12.0834 10.0003 12.0834Z" fill="currentColor"/><path d="M10.0003 15.4167C10.4606 15.4167 10.8337 15.0436 10.8337 14.5833C10.8337 14.1231 10.4606 13.75 10.0003 13.75C9.54009 13.75 9.16699 14.1231 9.16699 14.5833C9.16699 15.0436 9.54009 15.4167 10.0003 15.4167Z" fill="currentColor"/><path d="M13.3333 12.0834C13.7936 12.0834 14.1667 11.7103 14.1667 11.25C14.1667 10.7898 13.7936 10.4167 13.3333 10.4167C12.8731 10.4167 12.5 10.7898 12.5 11.25C12.5 11.7103 12.8731 12.0834 13.3333 12.0834Z" fill="currentColor"/><path d="M13.3333 15.4167C13.7936 15.4167 14.1667 15.0436 14.1667 14.5833C14.1667 14.1231 13.7936 13.75 13.3333 13.75C12.8731 13.75 12.5 14.1231 12.5 14.5833C12.5 15.0436 12.8731 15.4167 13.3333 15.4167Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.9587 5.41669V16.25C18.9587 17.0792 18.6295 17.8734 18.0437 18.46C17.457 19.0459 16.6628 19.375 15.8337 19.375H4.16699C3.33783 19.375 2.54366 19.0459 1.95699 18.46C1.37116 17.8734 1.04199 17.0792 1.04199 16.25V5.41669C1.04199 4.58752 1.37116 3.79335 1.95699 3.20669C2.54366 2.62085 3.33783 2.29169 4.16699 2.29169H15.8337C16.6628 2.29169 17.457 2.62085 18.0437 3.20669C18.6295 3.79335 18.9587 4.58752 18.9587 5.41669ZM17.7087 5.41669C17.7087 4.91919 17.5112 4.44252 17.1595 4.09085C16.8078 3.73919 16.3312 3.54169 15.8337 3.54169H4.16699C3.66949 3.54169 3.19283 3.73919 2.84116 4.09085C2.48949 4.44252 2.29199 4.91919 2.29199 5.41669V16.25C2.29199 16.7475 2.48949 17.2242 2.84116 17.5759C3.19283 17.9275 3.66949 18.125 4.16699 18.125H15.8337C16.3312 18.125 16.8078 17.9275 17.1595 17.5759C17.5112 17.2242 17.7087 16.7475 17.7087 16.25V5.41669Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.3337 6.875C18.6787 6.875 18.9587 7.155 18.9587 7.5C18.9587 7.845 18.6787 8.125 18.3337 8.125H1.66699C1.32199 8.125 1.04199 7.845 1.04199 7.5C1.04199 7.155 1.32199 6.875 1.66699 6.875H18.3337Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.709 1.25C12.709 0.905 12.989 0.625 13.334 0.625C13.679 0.625 13.959 0.905 13.959 1.25V4.58333C13.959 4.92833 13.679 5.20833 13.334 5.20833C12.989 5.20833 12.709 4.92833 12.709 4.58333V1.25Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04199 1.25C6.04199 0.905 6.32199 0.625 6.66699 0.625C7.01199 0.625 7.29199 0.905 7.29199 1.25V4.58333C7.29199 4.92833 7.01199 5.20833 6.66699 5.20833C6.32199 5.20833 6.04199 4.92833 6.04199 4.58333V1.25Z" fill="currentColor"/></svg>
								23 Jan 2026
							</div>
						</td>
						<td><button class="action-btn" aria-label="More options"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 7.5C12.8284 7.5 13.5 6.82843 13.5 6C13.5 5.17157 12.8284 4.5 12 4.5C11.1716 4.5 10.5 5.17157 10.5 6C10.5 6.82843 11.1716 7.5 12 7.5Z" fill="currentColor"/><path d="M12 13.5C12.8284 13.5 13.5 12.8284 13.5 12C13.5 11.1716 12.8284 10.5 12 10.5C11.1716 10.5 10.5 11.1716 10.5 12C10.5 12.8284 11.1716 13.5 12 13.5Z" fill="currentColor"/><path d="M12 19C12.8284 19 13.5 18.3284 13.5 17.5C13.5 16.6716 12.8284 16 12 16C11.1716 16 10.5 16.6716 10.5 17.5C10.5 18.3284 11.1716 19 12 19Z" fill="currentColor"/></svg></button></td>
					</tr>
					<tr>
						<td><div class="row-name"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/employee-sample.webp" alt="profile_photo"> KFC</div></td>
						<td>Viewed Profile</td>
						<td>
							<div class="date-cell">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M6.66732 12.0834C7.12756 12.0834 7.50065 11.7103 7.50065 11.25C7.50065 10.7898 7.12756 10.4167 6.66732 10.4167C6.20708 10.4167 5.83398 10.7898 5.83398 11.25C5.83398 11.7103 6.20708 12.0834 6.66732 12.0834Z" fill="currentColor"/><path d="M6.66732 15.4167C7.12756 15.4167 7.50065 15.0436 7.50065 14.5833C7.50065 14.1231 7.12756 13.75 6.66732 13.75C6.20708 13.75 5.83398 14.1231 5.83398 14.5833C5.83398 15.0436 6.20708 15.4167 6.66732 15.4167Z" fill="currentColor"/><path d="M10.0003 12.0834C10.4606 12.0834 10.8337 11.7103 10.8337 11.25C10.8337 10.7898 10.4606 10.4167 10.0003 10.4167C9.54009 10.4167 9.16699 10.7898 9.16699 11.25C9.16699 11.7103 9.54009 12.0834 10.0003 12.0834Z" fill="currentColor"/><path d="M10.0003 15.4167C10.4606 15.4167 10.8337 15.0436 10.8337 14.5833C10.8337 14.1231 10.4606 13.75 10.0003 13.75C9.54009 13.75 9.16699 14.1231 9.16699 14.5833C9.16699 15.0436 9.54009 15.4167 10.0003 15.4167Z" fill="currentColor"/><path d="M13.3333 12.0834C13.7936 12.0834 14.1667 11.7103 14.1667 11.25C14.1667 10.7898 13.7936 10.4167 13.3333 10.4167C12.8731 10.4167 12.5 10.7898 12.5 11.25C12.5 11.7103 12.8731 12.0834 13.3333 12.0834Z" fill="currentColor"/><path d="M13.3333 15.4167C13.7936 15.4167 14.1667 15.0436 14.1667 14.5833C14.1667 14.1231 13.7936 13.75 13.3333 13.75C12.8731 13.75 12.5 14.1231 12.5 14.5833C12.5 15.0436 12.8731 15.4167 13.3333 15.4167Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.9587 5.41669V16.25C18.9587 17.0792 18.6295 17.8734 18.0437 18.46C17.457 19.0459 16.6628 19.375 15.8337 19.375H4.16699C3.33783 19.375 2.54366 19.0459 1.95699 18.46C1.37116 17.8734 1.04199 17.0792 1.04199 16.25V5.41669C1.04199 4.58752 1.37116 3.79335 1.95699 3.20669C2.54366 2.62085 3.33783 2.29169 4.16699 2.29169H15.8337C16.6628 2.29169 17.457 2.62085 18.0437 3.20669C18.6295 3.79335 18.9587 4.58752 18.9587 5.41669ZM17.7087 5.41669C17.7087 4.91919 17.5112 4.44252 17.1595 4.09085C16.8078 3.73919 16.3312 3.54169 15.8337 3.54169H4.16699C3.66949 3.54169 3.19283 3.73919 2.84116 4.09085C2.48949 4.44252 2.29199 4.91919 2.29199 5.41669V16.25C2.29199 16.7475 2.48949 17.2242 2.84116 17.5759C3.19283 17.9275 3.66949 18.125 4.16699 18.125H15.8337C16.3312 18.125 16.8078 17.9275 17.1595 17.5759C17.5112 17.2242 17.7087 16.7475 17.7087 16.25V5.41669Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.3337 6.875C18.6787 6.875 18.9587 7.155 18.9587 7.5C18.9587 7.845 18.6787 8.125 18.3337 8.125H1.66699C1.32199 8.125 1.04199 7.845 1.04199 7.5C1.04199 7.155 1.32199 6.875 1.66699 6.875H18.3337Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.709 1.25C12.709 0.905 12.989 0.625 13.334 0.625C13.679 0.625 13.959 0.905 13.959 1.25V4.58333C13.959 4.92833 13.679 5.20833 13.334 5.20833C12.989 5.20833 12.709 4.92833 12.709 4.58333V1.25Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04199 1.25C6.04199 0.905 6.32199 0.625 6.66699 0.625C7.01199 0.625 7.29199 0.905 7.29199 1.25V4.58333C7.29199 4.92833 7.01199 5.20833 6.66699 5.20833C6.32199 5.20833 6.04199 4.92833 6.04199 4.58333V1.25Z" fill="currentColor"/></svg>
								23 Jan 2026
							</div>
						</td>
						<td><button class="action-btn" aria-label="More options"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 7.5C12.8284 7.5 13.5 6.82843 13.5 6C13.5 5.17157 12.8284 4.5 12 4.5C11.1716 4.5 10.5 5.17157 10.5 6C10.5 6.82843 11.1716 7.5 12 7.5Z" fill="currentColor"/><path d="M12 13.5C12.8284 13.5 13.5 12.8284 13.5 12C13.5 11.1716 12.8284 10.5 12 10.5C11.1716 10.5 10.5 11.1716 10.5 12C10.5 12.8284 11.1716 13.5 12 13.5Z" fill="currentColor"/><path d="M12 19C12.8284 19 13.5 18.3284 13.5 17.5C13.5 16.6716 12.8284 16 12 16C11.1716 16 10.5 16.6716 10.5 17.5C10.5 18.3284 11.1716 19 12 19Z" fill="currentColor"/></svg></button></td>
					</tr>
				</tbody>
			</table>
		</div>
	</main>
</div>
<?php
	return ob_get_clean();
}
add_shortcode('candidates_dashboard', 'candidates_dashboard');