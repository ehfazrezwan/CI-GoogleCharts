<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Google charts - Codeigniter server side</title>
  </head>
  <body>
    <div class="" id="gChart">

    </div>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <!-- Google charts library  -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){

        //Load core chart library
        google.charts.load('current', {'packages':['corechart']});
        //This calls the function to draw the chart
        google.charts.setOnLoadCallback(drawLiveChart);

        getTicks(); //Set the horizontal axis ticks upon document load

        //Set the options for the chart (some configurations are optional)
        //***********************
        optionsLiveTrx = {
          animation: {
            duration: 1000,
            easing: 'in'
          },
          hAxis: {
            title: "Time of day",
            ticks: ticks //The ticks here are the ones set in the getTicks() function
          },
          vAxis: {
            title: "Value"
          },
          legend: {
            position: 'none'
          },
          chartArea: {
            width: '80%'
          },
          pointSize: 5
        }
        //********************

        //This function gets grpah data from the controller via ajax and stores
        //it
        jsonDataLiveTrx = $.ajax({
          url: "<?php echo site_url('Main/liveTrxChart') ?>",
          dataType: "json",
          async: false
        }).responseText;

        //Set interval function used to request server for asynchronous
        //information every 60s. The graph is redrawn with the new data
        //and new horizontal axis ticks
        setInterval(function(){
          getTicks();
          optionsLiveTrx.hAxis.ticks = ticks;
          jsonDataLiveTrx = $.ajax({
            url: "<?php echo site_url('Main/liveTrxChart') ?>",
            dataType: "json",
            async: false
          }).responseText;
          drawLiveChart();
        }, 60000);

      });

      //This function gets the horizontal axis ticks for a certain time
      //and sets it on the google chart. This is useful and necessary
      //when dealing with realtime charts
      function getTicks(){
        $.ajax({
          url: "<?php echo site_url('Main/systemStartTime') ?>", //Controller sends JSON response of system start time (1 hour prior to current time)
          type: "POST",
          async: false,
          success: function(data){
            startTime = JSON.parse(data);
            // alert(details.minutes);
          }
        });

        startHour = startTime.hours;
        startMin = startTime.minutes;

        ticks = [];
        tickArray = [ticks];
        newHour = startHour;
        newMin = startMin;

        //Set the ticks for the horizontal axis, starting from the start time
        //obtained and continue doing so for the next hour, over 10 minute intervals
        for(i = 0; i < 7; i++){
          if(i == 0){
            ticks[i] = [startHour, startMin, 0];
          }else{
            newMin += 10;
            if(newMin >= 60){
              newHour += 1;
              newMin = 0;
              ticks[i] = [newHour, newMin, 0];
            }else{
              ticks[i] = [newHour, newMin, 0];
            }
          }
        }
      }
      //Function to draw the google chart, placing it in the div with the specified
      //ID
      function drawLiveChart(){
        //Chart data set to the data obtained via ajax
        data = new google.visualization.DataTable(jsonDataLiveTrx);

        chart = new google.visualization.LineChart(document.getElementById('gChart'));
        chart.draw(data, optionsLiveTrx);
      }
    </script>
  </body>
</html>
