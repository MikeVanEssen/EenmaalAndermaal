// http://www.sitepoint.com/build-javascript-countdown-timer-no-dependencies/

// berekend de tijd tussen nu en 'endtime'
function getTimeRemaining(endtime){
  var t         = Date.parse(endtime) - Date.now(); // parse zet datum 
  var seconds   = Math.floor( (t/1000) % 60 );
  var minutes   = Math.floor( (t/1000/60) % 60 );
  var hours     = Math.floor( (t/(1000*60*60)) % 24 );
  var days      = Math.floor( t/(1000*60*60*24) );

  return {
    'total': t,
    'days': days,
    'hours': hours,
    'minutes': minutes,
    'seconds': seconds
  };
}

// start de klok
function initializeClock(id, endtime){
  var clock = document.getElementById(id);
  var timeinterval = setInterval(function(){
    var t = getTimeRemaining(endtime);
    clock.innerHTML =   t.days + ' dagen ' + 
                        t.hours + ' uren<br>' +
                        t.minutes + ' minuten ' +
                        t.seconds + ' seconde';
    if(t.total<=0){
      clearInterval(timeinterval);
      clock.innerHTML = 'Gesloten';
    }
  },1000);
}