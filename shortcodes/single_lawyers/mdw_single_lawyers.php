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


add_shortcode('lawyer_jobs', 'lawyer_jobs_function');

function lawyer_jobs_function()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $allJobs = get_field('informacion_laboral');
  $html = '';
  foreach ($allJobs as $job) {
    $year = $job['tiempo_trabajo'];
    $position = $job['cargo'];
    $company = $job['lugar'];

    $html .= "
      <div class='mdw__lawyer_job'>
        <div class='mdw__job_year mdw-fw300'>$year</div>
        <div class='mdw__job_position mdw-fw400 mdw-fup'>$position</div>
        <div class='mdw__job_company mdw-fw700'>$company</div>
      </div>
    ";
  }
  return $html;
}


add_shortcode('lawyer_academic_activities', 'lawyer_academic_activities_function');

function lawyer_academic_activities_function()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $activities = get_field('actividades_academicas_docencia');
  $html = '';
  foreach ($activities as $activity) {
    $year = $activity['tiempo_fecha'];
    $description = $activity['descripcion_academica'];
    $ocupation = $activity['cargo'];

    $html .= "
      <div class='lawyer_academic_activities'>
        <div class='mdw__activity_year mdw-fw300'>$year</div>
        <div class='mdw__activity_description mdw-fw400'>$description</div>
        <div class='mdw__activity_ocupation mdw-fw700'>$ocupation</div>
      </div>
    ";
  }
  return $html;
}


add_shortcode('lawyer_language', 'lawyer_language');

function lawyer_language()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $lawyerId = get_the_ID();
  $languages = wp_get_post_terms($lawyerId, 'idiomas');
  $html = '';
  $htmlLanguage = '';
  $count = 0;
  foreach ($languages as $language) {
    $count++;
    $languageName = $language->name;
    $coma = $count === 1 ? '' : '<span>, </span>';
    $htmlLanguage .= "$coma$languageName";
  }
  $html .= "
    <div class='lawyer_language'>
      <div class='mdw__language_year mdw-fw400'>$htmlLanguage</div>
    </div>
  ";
  return $html;
}
