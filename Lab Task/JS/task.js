// Task 1
let a = 5;
let b = 10;
console.log(`Task 1 | Before swap: a = ${a}, b = ${b}`);

[a, b] = [b, a];

console.log(`Task 1 | After swap:  a = ${a}, b = ${b}\n`);


// Task 2
function square(n) {
  return n * n;
}

console.log("Task 2 | Squaring numbers 1 through 10:");
for (let i = 1; i <= 10; i++) {
  console.log(`  Square of ${i} is ${square(i)}`);
}
console.log("\n");


// Task 3
function findLargest(arr) {
  if (arr.length === 0) return undefined; 
  
  let largest = arr[0]; 
  
  for (let i = 1; i < arr.length; i++) {
    if (arr[i] > largest) {
      largest = arr[i]; 
    }
  }
  
  return largest;
}

const numbersArray = [23, 89, 12, 104, 5, 77];
const largestNumber = findLargest(numbersArray);

console.log(`Task 3 | The largest number in [${numbersArray}] is: ${largestNumber}`);