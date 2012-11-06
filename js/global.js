/**
 * Converts human time to UNIX time
 */
function humanToTime(month, day, year, hour, minute, second)
{
    console.log(month, day, year, hour, minute, second);
    var humDate = new Date(Date.UTC(year,
      (stripLeadingZeroes(month)-1),
      stripLeadingZeroes(day),
      stripLeadingZeroes(hour),
      stripLeadingZeroes(minute),
      stripLeadingZeroes(second)));
    return (humDate.getTime()/1000.0);
}

/**
 * Strips leading zero's from input (so 01 --> 1)
 */
function stripLeadingZeroes(input)
{
    if((input.length > 1) && (input.substr(0,1) == "0"))
      return input.substr(1);
    else
      return input;
}

/**
 * Returns a number padded with zeros until it is at least size
 *
 * ex: padWithZeros('15', 4) = 0015
 */
function padWithZeros(num, size) {
    var s = num+"";
    while (s.length < size) {
      s = "0" + s;
    }
    return s;
}

/**
 * Stops an event's propogation if it's at all possible
 */
function killEventWithFire(e) {
    e.cancel=true;
    e.returnValue=false;
    e.cancelBubble=true;
    if (e.stopPropagation) e.stopPropagation();
    if (e.preventDefault) e.preventDefault();
    return false;
}
