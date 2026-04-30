import sys
import cv2
import numpy as np
import json
from deepface import DeepFace
from deepface.commons import functions

def extract_face_features(image_path):
    try:
        # Read the image
        img = cv2.imread(image_path)
        if img is None:
            return json.dumps({"error": "Failed to read image"})

        # Convert BGR to RGB
        img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)

        # Detect face and extract features
        face_objs = DeepFace.extract_faces(img_path=image_path, target_size=(224, 224), detector_backend='opencv')
        
        if not face_objs or len(face_objs) == 0:
            return json.dumps({"error": "No face detected in image"})
        
        if len(face_objs) > 1:
            return json.dumps({"error": "Multiple faces detected in image"})

        # Get embeddings using VGG-Face
        embedding_objs = DeepFace.represent(img_path=image_path, model_name="VGG-Face", detector_backend='opencv')
        
        if not embedding_objs or len(embedding_objs) == 0:
            return json.dumps({"error": "Failed to extract face features"})

        # Return the first embedding
        return json.dumps({
            "features": embedding_objs[0]["embedding"]
        })

    except Exception as e:
        return json.dumps({"error": str(e)})

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(json.dumps({"error": "Please provide an image path"}))
        sys.exit(1)

    image_path = sys.argv[1]
    print(extract_face_features(image_path))
