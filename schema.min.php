<?php
ob_start();
echo <<<JSON
{"@context":"https://schema.org","@graph":[{"@type":"Person","@id":"{URL_WEBSITE}#me","name":"{BIO_FULL_NAME}","alternateName":"txufiknr","description":"{BIO_FULL_NAME} is a professional software engineer specializing in mobile and web application development. He enjoys building reliable, user-friendly, multi-platform solutions that make technology more accessible and effective.","url":"{URL_WEBSITE}","image":"{URL_WEBSITE}/{PATH_PHOTO}","jobTitle":"Senior Software Engineer","worksFor":{"@type":"Organization","@id":"https://nurosoft.id/#organization","name":"Nurosoft Consulting","url":"https://nurosoft.id"},"sameAs":["{URL_LINKEDIN}","{URL_GITHUB}","{URL_TWITTER}","{URL_PLAYSTORE}"],"gender":"Male","nationality":{"@type":"Country","name":"Indonesia"},"knowsLanguage":["en","id"],"knowsAbout":["Web Development","Mobile Applications","Frontend Development","Backend Development","Full-stack Development","Cross-platform Development","Flutter","React","Node.js","PHP","JavaScript"],"contactPoint":[{"@type":"ContactPoint","contactType":"Portfolio Contact","email":"flias.test@gmail.com","url":"https://wa.me/6285954479380","availableLanguage":{LANGUAGE_OPTIONS}}],"mainEntityOfPage":{"@id":"{URL_WEBSITE}#webpage"}},{"@type":"WebSite","@id":"{URL_WEBSITE}#website","url":"{URL_WEBSITE}","name":"{BIO_FULL_NAME} | Portfolio","publisher":{"@id":"{URL_WEBSITE}#me"}},{"@type":"WebPage","@id":"{URL_WEBSITE}#webpage","url":"{URL_WEBSITE}","name":"{BIO_FULL_NAME} | Portfolio","description":"{$description}","about":{"@id":"{URL_WEBSITE}#me"},"mainEntity":{"@id":"{URL_WEBSITE}#me"},"isPartOf":{"@id":"{URL_WEBSITE}#website"},"inLanguage":{LANGUAGE_OPTIONS}}]}
JSON;
$jsonTemplate = ob_get_clean();
echo strtr($jsonTemplate, [
  '{BIO_FULL_NAME}' => BIO_FULL_NAME,
  '{BIO_LEGAL_NAME}' => BIO_LEGAL_NAME,
  '{JOB_FIRST_YEAR}' => JOB_FIRST_YEAR,
  '{PATH_PHOTO}' => PATH_PHOTO,
  '{URL_WEBSITE}' => URL_WEBSITE,
  '{URL_LINKEDIN}' => URL_LINKEDIN,
  '{URL_GITHUB}' => URL_GITHUB,
  '{URL_TWITTER}' => URL_TWITTER,
  '{URL_PLAYSTORE}' => URL_PLAYSTORE,
  '{LANGUAGE_OPTIONS}' => json_encode(LANGUAGE_OPTIONS),
]);