const express = require('express');
const bodyParser = require('body-parser');
const storage = require('azure-storage');
const multer = require('multer');
const util = require('util');

require('dotenv').config();

const upload = multer();
const port = process.env.PORT || 1337;
const app = express();

app.set('view engine', 'pug');
app.set('views', './views');
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));

const blobService = storage.createBlobService(process.env.AZURE_STORAGE_CONNECTION_STRING);
const tableService = storage.createTableService(process.env.AZURE_STORAGE_CONNECTION_STRING);
const queueService = storage.createQueueService(process.env.AZURE_STORAGE_CONNECTION_STRING);

app.get('/', (req, res) => {
  res.render('index');
});

app.post('/', upload.single('file'), (req, res) => {
  blobService.createBlockBlobFromText('mathblob', req.file.originalname, req.file.buffer, (err, result, resp) => {
    if (!err) {
      const url = blobService.getUrl('mathblob', result.name);
      console.log('blob');
      console.log(result);

      tableService.insertEntity('mathtable', {
        PartitionKey: {
          _: 'adPartKey',
        },
        RowKey: {
          _: `adRowKey-${result.etag}`,
        },
        Description: {
          _: JSON.stringify({
            name: req.body.name,
            price: req.body.price,
            file: url,
          }),
        },
      }, (er) => {
        if (!er) {
          console.log('table');
          queueService.createMessage('mathqueue', `adRowKey-${result.etag}`, (error, resu) => {
            if (!error) {
              console.log('queue');
              console.log(resu);
              res.send('ok');
            } else {
              console.log(error);
            }
          });
        } else {
          console.log(er);
          res.send('pas ok');
        }
      });
    } else {
      console.log(err);
      res.send('pas ok');
    }
  });
});

app.listen(port, () => {
  blobService.createContainerIfNotExists('mathblob', () => {
    console.log('Container created');
  });
  tableService.createTableIfNotExists('mathtable', () => {
    console.log('Table created');
  });
  queueService.createQueueIfNotExists('mathqueue', () => {
    console.log('Queue created');
  });
  console.log(`server running on ${port}`);
});
