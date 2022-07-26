<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package performancein
 */

get_header();
$job_id             = get_the_ID();
$jobTitleProp       = get_the_title( $job_id );
$author_id          = get_post_field( 'post_author', $job_id );
$jobPublishDate     = get_the_date( 'F j, Y', $job_id );
$jobPublishDateProp = date( "F j, Y", strtotime( $jobPublishDate ) );
$jobPublishDateProp = date( "yy-m-d", strtotime( $jobPublishDate ) );

$recruiter_company_name = get_field( 'pi_recruiter_company_name', "user_{$author_id}" );
$recruiter_company_name = ( isset( $recruiter_company_name ) && ! empty( $recruiter_company_name ) ) ? $recruiter_company_name : '';

$recruiter_logo_url = pi_get_recruiter_logo( $author_id );

$job_field_values    = pi_get_job_field_values( $job_id );
$job_title           = $job_field_values['job_title'];
$job_type            = $job_field_values['job_type'];
$contract_length     = $job_field_values['contract_length'];
$geographic_location = $job_field_values['geographic_location'];
$description         = $job_field_values['description'];
$minimum_salary      = $job_field_values['minimum_salary'];
$maximum_salary      = $job_field_values['maximum_salary'];
$closing_date        = $job_field_values['closing_date'];
$contact_phone       = $job_field_values['contact_phone'];
$contact_email       = $job_field_values['contact_email'];
$jobs_employer       = $job_field_values['pi_jobs_employer'];
$pi_schema_streetadd = (!empty (get_post_field( 'pi_jobs_schema_streetaddress', $job_id ) ) ) ?  get_post_field( 'pi_jobs_schema_streetaddress', $job_id ) : ' ' ;
$pi_schema_postalcode = (!empty (get_post_field( 'pi_jobs_schema_postalcode', $job_id ) ) ) ?  get_post_field( 'pi_jobs_schema_postalcode', $job_id ) : ' ' ;
$pi_schema_addressregion = (!empty (get_post_field( 'pi_jobs_schema_addressregion', $job_id ) ) ) ?  get_post_field( 'pi_jobs_schema_addressregion', $job_id ) : ' ' ;
$pi_schema_addresscountry = (!empty (get_post_field( 'pi_jobs_schema_addresscountry', $job_id ) ) ) ?  get_post_field( 'pi_jobs_schema_addresscountry', $job_id ) : ' ' ;
$term_list_ids       = $job_field_values['term_list_ids'];
$itmPropStartDate    = date( "j M y", strtotime( $closing_date ) );
$itmPropStartDate    = date( "yy-m-d", strtotime( $closing_date ) );
$salary              = pi_get_salary( $minimum_salary, $maximum_salary );
$CurrencySymbol      = get_woocommerce_currency_symbol();
$minimum_salaryProp  = $minimum_salary;
$maximum_salaryProp  = $maximum_salary;
$product_id          = get_field( 'pi_jobs_packages', $job_id );
$is_featured         = pi_is_featured_package( $product_id );
$is_class            = '';
$is_expired          = false;
if ( pi_is_expired_job( $closing_date ) ) {
	$is_expired = true;
	$is_class   = 'job-expired';
}
$ItemPropSalaryCurrency = get_woocommerce_currency();
?>
	<div class="grid mainContent clearfix" id="js-mainContent" role="main">
		<section class="content contentWithSidebar">

			<article class="articlefull job <?php echo esc_attr( $is_class ); ?>" itemscope itemtype="http://schema.org/JobPosting">
				<header class="jobFull-header">
						<?php if ( isset( $recruiter_logo_url ) && ! empty( $recruiter_logo_url ) ) { ?>
							<a href="<?php the_permalink(); ?>" class="job-recruiter-logo">
								<img itemprop="image" src="<?php echo esc_url( $recruiter_logo_url ); ?>" alt="<?php echo esc_attr( $recruiter_company_name ); ?>">
							</a>
						<?php } ?>
					<h1>
						<?php if ( true === $is_expired ) { ?>
							<span class="job-expired-lable"><?php esc_html_e( 'Expired:', 'performancein' ); ?></span><br>
						<?php } ?>
						<span class="hiddenschemaurl" itemprop="title"><?php esc_html_e( $jobTitleProp, 'performancein' ); ?></span>
						<span><?php echo esc_html( $job_title ); ?><br>

						<?php if ( isset( $salary ) && '' !== $salary ) { ?>
							<span class="hiddenschemaurl" itemprop="salaryCurrency" content="<?php esc_html_e( $ItemPropSalaryCurrency, 'performancein' ); ?>"></span>
							<span class="hiddenschemaurl" itemprop="baseSalary" content="<?php esc_html_e( $minimum_salaryProp, 'performancein' ); ?>"></span>
							<span class="jobsalary"><?php echo esc_html( $salary ); ?></span>
							<span class="jobDivider">/</span>
						<?php } ?>
							<?php if ( isset( $job_type ) && ! empty( $job_type ) ) { ?>
								<span class="jobtype" class="jobtype" itemprop="employmentType"><?php echo esc_html( $job_type ); ?></span>
							<?php } ?>
					</h1>

					<h2>
						<?php if ( isset( $geographic_location ) && !empty( $geographic_location ) ) { ?>
							<span itemprop="jobLocation" itemscope itemtype="http://schema.org/Place">
								<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
								<span data-icon="&#xe013;"></span>
								<span itemprop="addressLocality"><?php echo esc_html( $geographic_location ); ?></span>
								<span class="hiddenschemaurl" itemprop="streetAddress"><?php esc_html_e( $pi_schema_streetadd, 'performancein' ); ?></span>
								<span class="hiddenschemaurl" itemprop="postalCode"><?php echo esc_html( $pi_schema_postalcode ); ?></span>
								<span class="hiddenschemaurl" itemprop="addressRegion"><?php echo esc_html( $pi_schema_addressregion ); ?></span>
								<span class="hiddenschemaurl" itemprop="addressCountry"><?php echo esc_html( $pi_schema_addresscountry ); ?></span>
								</span>
							</span> /
						<?php } ?>
							<?php if ( isset( $contract_length ) ) {?>
								<?php echo esc_html( $contract_length );
							} ?>

					</h2>
					<div class="meta">
						<?php
						if ( isset( $jobs_employer ) && ! empty( $jobs_employer ) ) {?>
                            <span class="recruiter" itemprop="hiringOrganization" itemscope itemtype="http://schema.org/Organization">
                                <span data-icon="&#xe029;"></span>
                                <span itemprop="name"><?php echo esc_html( $jobs_employer ); ?></span>
                            </span>
                       <?php } else {
							$author_id = get_post_field ('post_author', get_the_ID());
							$author_data = get_the_author_meta('pi_recruiter_company_name', $author_id );
							if( isset( $author_data ) && ! empty( $author_data ) ) { ?>
							     <span class="recruiter" itemprop="hiringOrganization" itemscope itemtype="http://schema.org/Organization">
                                    <span data-icon="&#xe029;"></span>
                                    <span itemprop="name"><?php echo esc_html( $author_data ); ?></span>
                                </span>
                        <?php }
						} ?>
						<span class="time">
                            <span data-icon="&#xe012;"></span>
                            <?php
                            $human_time = human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) );
                            printf( esc_html__( 'Posted %s ago', 'performancein' ), esc_attr( $human_time ) );
                            ?>
                        </span>
						<span class="hiddenschemaurl" itemprop="validThrough"><?php esc_html_e( $itmPropStartDate, 'performancein' ); ?></span>
						<span class="hiddenschemaurl" itemprop="datePosted"><?php esc_html_e( $jobPublishDateProp, 'performancein' ); ?></span>
						<span class="date"><span data-icon="&#xf073;"></span>
                            <?php
                            if ( true === $is_expired ) {
	                            esc_html_e( 'Expired', 'performancein' );
                            } else {
	                            esc_html_e( 'Closes: ', 'performancein' );
	                            echo esc_html( date_format( date_create( $closing_date ), "d/m/y" ) );
                            }
                            ?>
                    </span>
					</div>
				</header>
				<section class="articlecont">
					<header class="socialhead">
						<div id="socialfade">
							<?php echo do_shortcode( '[addtoany]' ); ?>
						</div>
					</header>
					<div itemprop="description">
						<?php
						echo wp_kses_post( $description ); ?>
					</div>
					<?php
					if ( false === $is_expired ) {
						wp_enqueue_script( 'performancein-custom' );
						?>
						<button id="job_toogle_form" class="button buttonFullWidth"><?php esc_html_e( 'Apply Now', 'performancein' ); ?></button>
						<section class="jobapplication">
							<form action="javascript:void(0);" class="job-apply-form" enctype="multipart/form-data" method="post" id="apply">
								<input type="hidden" id="id_jobs" value="<?php echo esc_attr( $job_id ); ?>">
								<input type="hidden" id="id_email" value="<?php echo esc_attr( $contact_email ); ?>">
								<input type="hidden" id="id_product" value="<?php echo esc_attr( $product_id ); ?>">
								<?php wp_nonce_field( 'job_apply_form_nonce', 'job_apply_form_name' ); ?>
								<div id="formfields">
									<div id="div_user_id_email" class="control-group">
										<input class="textinput textInput" id="user_id_email" name="email" placeholder="Email Address" type="text">
									</div>
									<div id="div_id_cover_description" class="control-group">
										<div class="controls">
											<textarea cols="40" id="id_cover_description" name="id_cover_description" placeholder="Covering Letter" rows="10"></textarea>
										</div>
									</div>
									<div id="div_id_resume" class="control-group">
										<label for="label_id_resume" class="control-label requiredField"><?php esc_html_e( 'Upload CV:', 'performancein' ); ?>
											<span class="asteriskField">*</span></label>
										<div class="controls">
											<input class="clearablefileinput" id="id_resume" name="id_resume" type="file" data-file_types="pdf|doc|docx">
										</div>
									</div>
								</div>
								<button id="job_apply_button" class="button buttonFullWidth"><?php esc_html_e( 'Apply Now', 'performancein' ); ?></button>
							</form>
						</section>
					<?php } ?>

					<footer class="socialfoot">
						<div id="socialftfade">
							<?php echo do_shortcode( '[addtoany]' ); ?>
						</div>
					</footer>

				</section>
			</article>
		</section>
		<?php get_sidebar(); ?>
	</div>

<?php
get_footer();
