require('../css/app.scss');
const $ = require('jquery');
require('bootstrap');

$(document).ready(function() {
  $('[data-toggle="tooltip"]').tooltip();
  $('[data-toggle="popover"]').popover();
});
