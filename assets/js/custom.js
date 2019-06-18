$(document).ready(function() {

  $("main#spapp > section").height($(document).height() - 60);

  var app = $.spapp({pageNotFound : 'error_404'}); // initialize

  // define routes

  app.route({view: 'babies', load: 'babies.html' });
  app.route({view: 'home', load: 'home.html' });
  app.route({view: 'register', load: 'register.html' });
  app.route({view: 'service', load: 'service.html' });
  app.route({
      view: 'logout',
      onCreate: function() {
        localStorage.clear();
        window.location.href = "index.html";
      },
    });


  // run app
  app.run();

});
