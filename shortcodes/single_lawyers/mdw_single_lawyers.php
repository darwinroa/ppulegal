<?php
add_shortcode('lawyer_education', 'lawyer_education_function');

function lawyer_education_function()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $allEducation = get_field('educacion');
  $html = '';
  if (! empty($allEducation) && ! is_wp_error($allEducation)) {
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
  }
  return $html;
}


add_shortcode('lawyer_jobs', 'lawyer_jobs_function');

function lawyer_jobs_function()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $allJobs = get_field('informacion_laboral');
  $html = '';
  if (! empty($allJobs) && ! is_wp_error($allJobs)) {
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
  }
  return $html;
}


add_shortcode('lawyer_academic_activities', 'lawyer_academic_activities_function');

function lawyer_academic_activities_function()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $activities = get_field('actividades_academicas_docencia');
  $html = '';
  if (! empty($activities) && ! is_wp_error($activities)) {
    foreach ($activities as $activity) {
      $year = $activity['tiempo_fecha'];
      $description = $activity['descripcion_academica'];
      $ocupation = $activity['cargo'];

      $html .= "
        <div class='mdw__lawyer_academic_activities'>
          <div class='mdw__activity_year mdw-fw300'>$year</div>
          <div class='mdw__activity_description mdw-fw400'>$description</div>
          <div class='mdw__activity_ocupation mdw-fw700'>$ocupation</div>
        </div>
      ";
    }
  }
  return $html;
}


add_shortcode('lawyer_language', 'lawyer_language_function');

function lawyer_language_function()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $lawyerId = get_the_ID();
  $languages = wp_get_post_terms($lawyerId, 'idiomas');
  $html = '';
  $htmlLanguage = '';
  $count = 0;
  if (! empty($languages) && ! is_wp_error($languages)) {
    foreach ($languages as $language) {
      $count++;
      $languageName = $language->name;
      $coma = $count === 1 ? '' : '<span>, </span>';
      $htmlLanguage .= "$coma$languageName";
    }
  }
  $html .= "
    <div class='lawyer_language'>
      <div class='mdw__language_year mdw-fw400'>$htmlLanguage</div>
    </div>
  ";
  return $html;
}



// add_shortcode('lawyer_posts', 'lawyer_posts_function');

// function lawyer_posts_function()
// {
//   wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

//   // Obtener el ID del abogado actual (post ID en el single de bd-abogados)
//   $lawyerId = get_the_ID();

//   // Configurar la consulta para obtener los posts de ppulegal relacionados con este abogado
//   $args = array(
//     'post_type' => 'ppulegal',
//     'meta_query' => array(
//       array(
//         'key' => 'abogados', // Nombre del ACF de relación
//         'value' => '"' . $lawyerId . '"',
//         'compare' => 'LIKE'
//       )
//     )
//   );

//   // Ejecutar la consulta
//   $query = new WP_Query($args);

//   // Verificar si hay publicaciones relacionadas
//   if ($query->have_posts()) {
//     $output = '<ul>';

//     // Recorrer los posts y generar la salida
//     while ($query->have_posts()) {
//       $query->the_post();
//       $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
//     }

//     $output .= '</ul>';
//   } else {
//     $output = '<p>No hay publicaciones relacionadas.</p>';
//   }

//   // Restaurar el post original
//   wp_reset_postdata();

//   return $output;
// }


add_shortcode('lawyer_membership', 'lawyer_membership_function');

function lawyer_membership_function()
{
  wp_enqueue_style('mdw-education-style', get_stylesheet_directory_uri() . '/shortcodes/single_lawyers/mdw_single_lawyers.css', array(), '1.0');

  $memberships = get_field('membresias');
  $html = '';
  $membershipHTML = '';
  if (! empty($memberships) && ! is_wp_error($memberships)) {
    foreach ($memberships as $membership) {
      $membershipLi = $membership['membresia'];
      $membershipHTML .= "<li>$membershipLi</li>";
    }

    $html .= "
          <div class='lawyer_membership'>
            <ul class='mdw__membership mdw-fw300'>$membershipHTML</ul>
          </div>
        ";
  }
  return $html;
}
