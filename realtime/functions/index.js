const functions = require("firebase-functions");
const express = require("express");

// // Create and Deploy Your First Cloud Functions
// // https://firebase.google.com/docs/functions/write-firebase-functions
//
const app = express();
app.use(express.static(__dirname + "/view"));

app.get("/timestamp", (req, res)=>{
  res.send(`${Date.now()}`);
});

app.get("/didier", (req, res)=>{
  res.sendFile(__dirname + "/didy.html");
});

app.get("/timestamp-cashed", (req, res)=>{
  res.set("Cashe-Control", "public, max-age=300, s-maxage=600");
  res.send(`${Date.now()}`);
});

exports.app = functions.https.onRequest(app);
