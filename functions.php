<?php
function format_phone($number) {
  return sprintf("+%s %s-%s-%s",
    substr($number, 0, 2),
    substr($number, 2, 3),
    substr($number, 5, 4),
    substr($number, 9));
}
function str_replace_first($search, $replace, $subject) {
  $search = '/'.preg_quote($search, '/').'/';
  return preg_replace($search, $replace, $subject, 1);
}
function is_mobile() {
  return preg_match("/\b(?:a(?:ndroid|vantgo)|b(?:lackberry|olt|o?ost)|cricket|docomo|hiptop|i(?:emobile|p[ao]d)|kitkat|m(?:ini|obi)|palm|(?:i|smart|windows )phone|symbian|up\.(?:browser|link)|tablet(?: browser| pc)|(?:hp-|rim |sony )tablet|w(?:ebos|indows ce|os))/i", $_SERVER["HTTP_USER_AGENT"]);
}