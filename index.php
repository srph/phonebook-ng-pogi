<!--
 Phonebook ng Pogi
 @author Kier Borromeo (srph)
 https://github.com/srph
 https://twitter.com/_srph
 @license MIT
 *
 * |  | _|__| ___________  ______   ____   ____ |__|
 * |  |/ /  |/ __ \_  __ \ \____ \ /  _ \ / ___\|  |
 * |    <|  \  ___/|  | \/ |  |_> >  <_> ) /_/  >  |
 * |__|_ \__|\___  >__|    |   __/ \____/\___  /|__|
 *      \/       \/        |__|         /_____/     
-->

<!DOCTYPE html>
<html>
<head>
  <title>Phonebook ng Pogi</title>
  <meta charset="utf-8">

  <style>
    .container {
      width: 768px; /* We don't really need that much space */
      margin-top: 25px;
      margin-bottom: 25px;
      margin-left: auto;
      margin-right: auto;
    }

    .search-form {
      margin-bottom: 50px;
    }

    .line {
      height: 2px;
      width: 50px;
      border-bottom: 1px solid #ddd;
    }

    img {
      float: left;
      margin-right: 10px;
    }

    h1, h2, h3, h4, h5, h6 {
      margin-top: 5px;
      margin-bottom: 5px;
    }
  </style>
</head>
<body>
  <div class="container">
    <form class="search-form">
      <input type="text" name="search" class="search-bar">
      <button type="submit" class="search-btn"> Search </button>
    </form>

    <div class="phonebook-container">
    </div>

    <footer>
      <a href="https://github.com/srph/phonebook-ng-pogi"><em>Phonebook ng Pogi</em></a> by
      <a href="https://srph.github.io"><strong>Kier Borromeo</strong> (<strong>@srph</strong>)</a>.
      <a href="https://github.com/srph"><small> @srph (Github) </small></a> |
      <a href="https://twitter.com/_srph"><small> @_srph (Twitter) </small></a>
    </footer>
  </div>

  <!-- Don't forget to include jQuery, hah! -->
  <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
  <script>
    // Here we invoke an `IIFE`
    // So we DO NOT _pollute_ the `global namespace`
    +function($) {
      // We prefix the variable with `$` ONLY
      // because it's just a symbol thing to let
      // you know it's a fucking element. But really,
      // any `variable name` will work.
      var $form = $('.search-form');
      var $btn = $('.search-btn'); // Or we can do this instead: `var $btn = $form.children('button');
      var $phonebook = $('.phonebook-container'); // Get the element`s` with the class of phonebook-container

      // To check if the search isn't done yet.
      var _loading = true;

      // We call the function 'loadPhonebook' which
      // performs an `AJAX` call (THE MAIN THING!!!)
      // _ONLY_ to initialize data.
      loadPhonebook();

      // We add an event listener. Which means, you get
      // to know that the user clicked the button, so you can do something
      $form.on('submit', function(evt) {
        // By default, our form refreshes the page,
        // this prevents it because we don't need it;
        // We're doing `AJAX`
        evt.preventDefault();

        if ( !_loading ) {
          // Not very important, but here are some notes:
          // evt.target contains the element which the event occured
          // evt.target is an array, thus `evt.target[0]`
          // You may get the element's value by its `value` property, thus
          // `evt.target[0].value`
          loadPhonebook(evt.target[0].value);
        }
      });

      /**
       * Our whole ajax thing.
       *
       * Why did you put it in a function?
       * We put it in a function so it's reusable. If you observe, you will
       * notice that it is being used for around 2~ times in this code.
       */
      function loadPhonebook(input) {
        // Disable the button when we start searching
        // then we remove after our searching (or `AJAX`) is finished
        $btn.attr('disabled', 'disabled');

        $.ajax({
          url: 'database.php',
          // Adds a query parameter (?key=value&key2=value)
          data: { 'search': input || '' },

          // Request type
          type: 'GET',

          // No need for this since our API / PHP File / Server
          // returns its response as `application/json``` already.
          //
          // But in any case:
          // We type in 'json' as data Type to transform
          // (obviously) the type of the response.
          // In case you forgot to include this option,
          // the first argument (response) in the `success`
          // function below will be a string instead of JSON
          /* dataType: 'json', */

          // This happens if our request to the server is successful
          success: function(response) {
            $btn.removeAttr('disabled'); // Enable the button again
            _loading = false; // We're not loading anymore

            var phonebook = response;

            // First, we check if our database returned
            // any result.
            if ( !phonebook.length ) {
              // If no results, let's remove whatever
              // is in the phonebook container, and replace
              // it with a message
              var message = 'No results for ' + input;
              $phonebook.html('<h1>' + message + '</h1>');
              return; // To stop the whole thing
              // Why not if then else instead?
              // 1 extra line is better than 10+ lines with extra indentation
            }

            // Remove whatever entry (element) is in the phonebook container
            $phonebook.empty();

            // @see createEntryTemplate
            phonebook.forEach(function(entry, index) {
              // Get the template from the createEntryTemplate
              var template = createEntryTemplate(entry);

              // Then we append that template to the phonebook container (.phonebook-container)
              // This happens:
              // <div class="phonebook-container">
              //   <!-- entry1 template -->
              // </div>
              // <div class="phonebook-container">
              //   <!-- entry1 template -->
              //   <!-- entry2 template -->
              // </div>
              $phonebook.append( template );
            });
          },

          // This happens if our request to the server fails
          // You may test this by deleting `database.php`.
          // Don't forget to backup!
          error: function() {
            alert('An error has occured. Please try again.. Refreshing');
            window.location.reload();
          }
        })
      }

      /**
       * Creates a template from the entry's (provided entry) data
       */
      function createEntryTemplate(data) {
        return $(
          '<div style="margin-bottom: 25px;">' +
            '<img src="images/' + data.avatar + '" width="64" height="64" style="border-radius: 50%;" />' +
            '<h3>' + data.name + '</h3>' +
            '<h4>' + data.number + '</h4>' +
            '<h6>' + data.address + '</h6>' +
            '<div class="line"></div>' +
          '</div>'
        );
      }
    }(jQuery);
  </script>
</body>
</html>
