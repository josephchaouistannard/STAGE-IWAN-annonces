import json
import chardet # You might need to install this: pip install chardet

input_file = 'db.json'
output_file = 'your_clean_file_utf8.json'

# --- Step 1: Try reading with a common encoding (UTF-8 first) ---
try:
    with open(input_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
    print("Successfully read with UTF-8.")

except UnicodeDecodeError:
    print("Failed to read with UTF-8. Trying to detect encoding...")

    # --- Step 2: If UTF-8 fails, try to detect encoding ---
    rawdata = open(input_file, 'rb').read() # Read as bytes
    result = chardet.detect(rawdata)
    detected_encoding = result['encoding']
    confidence = result['confidence']

    print(f"Detected encoding: {detected_encoding} with confidence {confidence}")

    if detected_encoding:
        try:
            with open(input_file, 'r', encoding=detected_encoding) as f:
                 data = json.load(f)
            print(f"Successfully read with detected encoding: {detected_encoding}")

        except (UnicodeDecodeError, json.JSONDecodeError) as e:
             print(f"Failed to read with detected encoding {detected_encoding}: {e}")
             print("Manual intervention needed. Try other encodings manually (e.g., latin-1, cp1252).")
             data = None # Indicate failure

    else:
        print("Could not detect encoding. Manual intervention needed.")
        data = None


# --- Step 3: If data was successfully loaded, save it as UTF-8 ---
if data is not None:
    try:
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=4) # Use indent for readability
        print(f"Successfully saved data to {output_file} with UTF-8 encoding.")

    except Exception as e:
        print(f"Error saving the file: {e}")

else:
    print("Could not load the data due to encoding issues.")