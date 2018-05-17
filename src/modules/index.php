<?php header('Content-type:application/json; charset=utf-8');header("Access-Control-Allow-Origin: *");header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization");header('HTTP/1.0 403 Forbidden');echo '{
  "status": "error",
  "code": "403",
  "message": "This page is forbidden."
}';?>