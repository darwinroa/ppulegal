<?php
add_shortcode('lawyer_education', 'lawyer_education_function');

function lawyer_education_function()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $allEducation = get_field('educacion');
  $html = '';
  foreach ($allEducation as $education) {
    $year = $education['ano_estudio'];
    $degree = $education['titulo'];
    $career = $education['carrera'];
    $university = $education['universidad'];

    $html .= "
    <div class='mdw__lawyer_education'>
      <div class='mdw__education_year mdw-fw300'>$year</div>
      <div class='mdw__education_degree mdw-fw400 mdw-fup'>$degree</div>
      <div class='mdw__education_career mdw-fw400'$career></div>
      <div class='mdw__education_university mdw-fw700'>$university</div>
    </div>
  ";
  }
  return $html;
}


add_shortcode('lawyer_jobs', 'lawyer_jobs');

function lawyer_jobs()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $allJobs = get_field('informacion_laboral');
  $html = '';
?>
  <pre>
  <?php
  // var_dump($allJobs);
  ?>
  </pre>
<?php
  foreach ($allJobs as $job) {
    $year = $job['tiempo_trabajo'];
    $position = $job['cargo'];
    $company = $job['lugar'];

    $html .= "
    <div class='mdw__lajobs'>
      <div class='mdw__job_year mdw-fw300'>$year</div>
      <div class='mdw__job_degree mdw-fw400 mdw-fup'>$position</div>
      <div class='mdw__job_university mdw-fw700'>$company</div>
    </div>
  ";
  }
  return $html;
}
?>