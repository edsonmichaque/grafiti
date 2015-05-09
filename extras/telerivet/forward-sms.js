var url = 'https://grafiti.herokuapp.com/api/sms/';

var smsObj = {
  source_number: from_number,
  destination_number: to_number,
  date_sent: new Date(),
  date_received: new Date(),
  content: content
};

var sms = JSON.stringify(smsObj);

var response = httpClient.request(url, {
  method: "POST",
  data: sms,
});
