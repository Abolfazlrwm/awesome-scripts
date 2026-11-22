// ██ ██   ██  █████  ███    ███ ██ ██████   ██████  ██████  ███    ███     
// ██  ██ ██  ██   ██ ████  ████ ██ ██   ██ ██      ██    ██ ████  ████     
// ██   ███   ███████ ██ ████ ██ ██ ██████  ██      ██    ██ ██ ████ ██     
// ██  ██ ██  ██   ██ ██  ██  ██ ██ ██   ██ ██      ██    ██ ██  ██  ██     
// ██ ██   ██ ██   ██ ██      ██ ██ ██   ██  ██████  ██████  ██      ██ on GitHub : https://github.com/ixAmirCom


//--------------------برنامه های ساده با جاوا اسکریپت (تمرینی)-----------------------//



// برنامه ای بنویسید که هر عددی بهش دادیم به اون تعداد سطر بسازه و مثلث درست کنه 

rows = prompt("Enter rows :");
for( i = 1; i <= rows; i++){
console.log("* ".repeat(i));
}

// برنامه ای بنویسید که هر عددی بهش دادیم به اون تعداد سطر بسازه و هرم درست کنه 

rows = prompt("Enter rows :");
for( i = 1; i <= rows; i++){
console.log(" ".repeat(rows - i) + "* ".repeat(i));
}

//--------------برنامه ی حدس عدد (بازی با کامپیوتر)----------------//

var Magic_number = Math.floor(Math.random() * 10);
var my_number;

while (Magic_number != my_number) {
  my_number = parseInt(prompt("Enter your guess: "), 10);

  if (Magic_number == my_number) {
    console.log("YOU WON");
  } else if (Magic_number < my_number) {
    console.log("The number is less than " + my_number);
  } else {
    console.log("The number is greater than " + my_number);
  }
}


// این برنامه تعداد دفعات تلاشم برای حدس عدد میگه

var Magic_number = Math.floor(Math.random() * 10);
var my_number , tries = 0;

while (Magic_number != my_number) {
  my_number = parseInt(prompt("Enter your guess: "), 10);
  tries++;
  if (Magic_number == my_number) {
    console.log("YOU WON BY " + tries + " tries! ");
  } else if (Magic_number < my_number) {
    console.log("The number is less than " + my_number);
  } else {
    console.log("The number is greater than " + my_number);
  }
}
