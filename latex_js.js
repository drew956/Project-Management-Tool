function reducer (prior, current, index, arr) {
  let exp = arr.length - 1 - index;
  let result = prior;

  if( current >= 0 && index != 0){
  	result += " + ";
  }
  if (current != 1 || exp == 0 ){
    if(current == -1){
	  	result += "-";
    }else{
      	result += current;
    }
  }
  if(exp != 0){
	if(exp == 1){
    	result += "x";
    }else {
    	result += "x^" + exp;
    }
  }

  return result;
}

function fracLatex(num, denom){
  return `\\frac\n{\n  ${num} \n}\n{\n  ${denom} \n}\n`;
}
function polyLatex(coefs){
	return coefs.reduce(reducer, "");
}
function polynomialLimitLatex(coefs1, coefs2, x0){
  let result = "";
  result += "\\lim_{x\\rightarrow " + x0 + "}\n";
  result += fracLatex(polyLatex(coefs1), polyLatex(coefs2));
  return result;
}

navigator.permissions.query({name: "clipboard-write"}).then(result => {
  if (result.state == "granted" || result.state == "prompt") {
     let res = window.prompt("What would you like to do? (1 - fraction, 2 - polynomial, 3 - limit of polynomials )?");
     let newClip = "";
     let data = "";
     switch(res){
         case "1":
            data = window.prompt("Enter a,b in the textbox for fraction a/b in Latex:");
            data = data.split(",");
            newClip = fracLatex(data[0],data[1]);
            break;
         case "2":
             data = window.prompt("Enter a,b,c,d,.. in the textbox for polynomial a x^n + bx^(n-1) + cx^(n-2) +...:");
             data = data.split(",");
             newClip = polyLatex(data);
            break;
         case "3":
             data = window.prompt("Enter a1,a2,..,an;b1,b2,...bn;x0 in the textbox for limit of ratio of polys as x goes to x0:");
             data = data.split(";");
             let coefs1 = data[0].split(",");
             let coefs2 = data[1].split(",");
             let x0 = data[2];
             newClip = polynomialLimitLatex(coefs1, coefs2, x0);
            break;
         default:
            alert("Wrong input!");
         break;
     }

     document.write("<textarea type='text' id='text' /></textarea> <button onclick='document.querySelector('#text').select();document.execCommand('copy');'>Copy Text</button>");
     document.close();
     let el = document.querySelector("#text");
     el.value = newClip;


  }
});