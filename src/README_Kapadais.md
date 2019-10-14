just install slim framework (composer require slim/slim:3.0) and run php -S localhost:8888 for dev server to start
mysql credentials are at index.php file

database's table is created using:
(sql queries)
CREATE TABLE ships_positions( id INT NOT NULL AUTO_INCREMENT,
                              mmsi INT NOT NULL ,
                              status INT NOT NULL ,
                              stationId INT NOT NULL ,
                              speed INT NOT NULL ,
                              lon FLOAT NOT NULL ,
                              lat FLOAT NOT NULL ,
                              course INT NOT NULL ,
                              heading INT NOT NULL ,
                              rot INT,
                              timestamp TIMESTAMP NOT NULL ,
                              PRIMARY KEY (id));



LOAD DATA LOCAL INFILE '~/Documents/ships_positions.csv' INTO TABLE ships_positions 
                                                         FIELDS TERMINATED BY ','
                                                         LINES TERMINATED BY '\n' 
                                                         IGNORE 1 ROWS (mmsi,
                                                         status, 
                                                         stationId,
                                                         speed,lon,
                                                         lat ,
                                                         course,
                                                         heading,
                                                         rot,
                                                         @timestamp)SET timestamp = FROM_UNIXTIME(@timestamp);
                                                                      
                                                                      
ships_position.csv is created by parsing ships_positions.json and create a csv file with pandas (python package) 

some basic postman's requests are provided                                                                
