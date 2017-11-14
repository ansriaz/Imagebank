// copy data from one to other

INSERT INTO imagebank.images (id, name, photo_id, filename, owner, secret, server, farm, title, is_public, license, link, uri, tags, source, date_uploaded,date_last_update, ownername, views, contributorlocation, accuracy, description, version, user_id, dataset_id, pathalias, machine_tags, place_id, woeid, geo_is_public, media, media_status, class_id)
  SELECT id, name, photo_id, filename, owner, secret, server, farm, title, is_public, license, link, uri, tags, source, date_uploaded,date_last_update, ownername, views, contributorlocation, accuracy, description, version, user_id, dataset_id, pathalias, machine_tags, place_id, woeid, geo_is_public, media, media_status, class_id FROM image_bank.web_image;

// copy images class labels 

INSERT INTO imagebank.imageclasslabels (id,image_id,class_id,remember_token,created_at,updated_at)
SELECT id,image_id,class_id,remember_token,created_at,updated_at FROM image_bank.imageclasslabels


// images url

INSERT INTO imagebank.imageurls (id, name, imageid, filename, original, ssmall, qlarge, msmall, nsmall, medium, zmediun, cmedium, blarge, hlarge, klarge, remember_token, created_at, updated_at, thumbnail)
  SELECT id, name, imageid, filename, original, ssmall, qlarge, msmall, nsmall, medium, zmediun, cmedium, blarge, hlarge, klarge, remember_token, created_at, updated_at, thumbnail FROM image_bank.imageurls

  