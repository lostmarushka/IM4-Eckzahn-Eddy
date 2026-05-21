/******************************************************************************************
 * ESP32-C6
 * TOUCHSENSOR + MPU6500 + WLAN + DATENBANK
 *
 * NEU:
 * - Sendet nur wenn TOUCH + BEWEGUNG aktiv sind
 * - kein sofortiges Senden mehr pro Sensor
 ******************************************************************************************/

#include <WiFi.h>
#include <HTTPClient.h>
#include <Arduino_JSON.h>
#include <Wire.h>
#include <MPU6050_light.h>

// =====================================================
// WLAN
// =====================================================

const char* ssid = "tinkergarden";
const char* pass = "strenggeheim";

const char* serverURL = "https://eckzahneddy.marina-lampert.ch/api/load.php";

// =====================================================
// TOUCH
// =====================================================

const int touchPin = 10;
bool touchActive = false;
bool touchSent = false;

// =====================================================
// MPU6500
// =====================================================

MPU6050 mpu(Wire);
bool mpu_ok = false;

float filteredDelta = 0;
float motionThreshold = 0.3;

bool motionActive = false;

unsigned long lastMotionSend = 0;
unsigned long motionCooldown = 1000;

// =====================================================
// WLAN STATUS
// =====================================================

unsigned long wifiLostTime = 0;
bool wifiWasConnected = true;

const unsigned long wifiFailDelay = 5000;
unsigned long lastReconnectTry = 0;
const unsigned long reconnectInterval = 10000;

// =====================================================
// SETUP
// =====================================================

void setup() {

  delay(1500);
  Serial.begin(115200);
  delay(1500);

  pinMode(LED_BUILTIN, OUTPUT);

  // Touch
  pinMode(touchPin, INPUT_PULLDOWN);

  // WLAN
  connectWiFi();

  // I2C MPU
  Wire.begin(7, 8);

  Serial.println("Starte MPU6500...");

  byte status = mpu.begin();

  if (status != 0) {
    Serial.println("⚠ MPU Fehler");
    mpu_ok = false;
  } else {
    Serial.println("✅ MPU verbunden");
    mpu_ok = true;
  }

  if (mpu_ok) {
    Serial.println("Kalibriere MPU...");
    delay(2000);
    mpu.calcOffsets(true, true);
    Serial.println("✅ Kalibrierung fertig");
  }
}

// =====================================================
// LOOP
// =====================================================

void loop() {

  handleWiFi();

  // =====================================================
  // TOUCH SENSOR (nur Zustand)
  // =====================================================

  int touchState = digitalRead(touchPin);

  if (touchState == HIGH) {
    touchActive = true;
  } else {
    touchActive = false;
    touchSent = false; // reset wenn losgelassen
  }

  // =====================================================
  // MPU SENSOR (nur Zustand)
  // =====================================================

  if (mpu_ok) {

    mpu.update();

    float ax = mpu.getAccX();
    float ay = mpu.getAccY();
    float az = mpu.getAccZ();

    float accMag = sqrt(ax * ax + ay * ay + az * az);
    float delta = fabs(accMag - 1.0);

    filteredDelta = (filteredDelta * 0.8) + (delta * 0.2);
    delta = filteredDelta;

    Serial.print("Delta: ");
    Serial.println(delta);

    if (delta > motionThreshold) {
      motionActive = true;
    } else {
      motionActive = false;
    }
  }

  // =====================================================
  // SEND LOGIK (NEU)
  // =====================================================

  if (touchActive && motionActive) {

    if (!touchSent && (millis() - lastMotionSend > motionCooldown)) {

      Serial.println("🚀 Touch + Bewegung aktiv → Sende Daten");

      sendTouch();
      sendMotion(filteredDelta);

      lastMotionSend = millis();
      touchSent = true;
    }
  }

  delay(50);
}

// =====================================================
// TOUCH SEND
// =====================================================

void sendTouch() {

  JSONVar data;

  data["sensor"] = "touchsensor";
  data["wert"] = 1;
  data["kinder_id"] = 1;

  sendJSON(JSON.stringify(data));
}

// =====================================================
// MOTION SEND
// =====================================================

void sendMotion(float delta) {

  JSONVar data;

  data["sensor"] = "lagesensor";
  data["wert"] = delta;
  data["kinder_id"] = 1;

  sendJSON(JSON.stringify(data));
}

// =====================================================
// HTTP SEND
// =====================================================

void sendJSON(String json) {

  Serial.println("Sende:");
  Serial.println(json);

  if (WiFi.status() == WL_CONNECTED) {

    HTTPClient http;
    http.begin(serverURL);
    http.addHeader("Content-Type", "application/json");

    int code = http.POST(json);

    Serial.print("HTTP: ");
    Serial.println(code);

    Serial.println(http.getString());

    http.end();

  } else {
    Serial.println("❌ WLAN nicht verbunden");
  }
}

// =====================================================
// WLAN
// =====================================================

void connectWiFi() {

  WiFi.disconnect(true);
  delay(1000);

  WiFi.begin(ssid, pass);

  int attempts = 0;

  while (WiFi.status() != WL_CONNECTED && attempts < 40) {
    delay(500);
    Serial.print(".");
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {

    Serial.println("\n✅ WLAN verbunden");
    Serial.println(WiFi.localIP());

  } else {
    Serial.println("\n❌ WLAN Fehler");
  }
}

// =====================================================
// WIFI HANDLER
// =====================================================

void handleWiFi() {

  if (WiFi.status() == WL_CONNECTED) {
    wifiLostTime = 0;
    wifiWasConnected = true;
    return;
  }

  if (wifiLostTime == 0) {
    wifiLostTime = millis();
    return;
  }

  if (millis() - wifiLostTime < wifiFailDelay) {
    return;
  }

  if (millis() - lastReconnectTry > reconnectInterval) {

    Serial.println("🔄 WLAN reconnect...");

    WiFi.disconnect();
    WiFi.reconnect();

    lastReconnectTry = millis();
  }
}