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
	$limit = 2;
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
	$limit = 2;

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