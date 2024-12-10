package com.pip.sensorskwbot.utils;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.ImageFormat;
import android.graphics.Rect;
import android.media.Image;
import android.renderscript.Allocation;
import android.renderscript.Element;
import android.renderscript.RenderScript;
import android.renderscript.ScriptIntrinsicYuvToRGB;
import android.renderscript.Type;

import java.nio.ByteBuffer;

public class YuvToRgbConverter {
    private RenderScript rs;
    private ScriptIntrinsicYuvToRGB scriptYuvToRgb;

    private int pixelCount = -1;
    private byte[] yuvBuffer = null;
    private Allocation inputAllocation = null;
    private Allocation outputAllocation = null;
    public YuvToRgbConverter(Context context){
        rs = RenderScript.create(context);
        scriptYuvToRgb = ScriptIntrinsicYuvToRGB.create(rs, Element.U8_4(rs));
    }

    public void yuvToRgb(Image image, Bitmap bitmapBuffer) {
        if(yuvBuffer!=null){
            pixelCount = image.getCropRect().width() * image.getHeight();
        }
    }

    public void convertYuvToRgb(byte[] yuv, int width, int height, int[] rgbOutput) {
        yuvBuffer = yuv;

        final int frameSize = width * height;
        final int uvStart = frameSize;

        for (int j = 0; j < height; j++) {
            for (int i = 0; i < width; i++) {
                int y = yuv[j * width + i] & 0xFF; // Y is a single byte
                int uvIndex = uvStart + (j >> 1) * width + (i & ~1); // U and V are interleaved
                int u = yuv[uvIndex] & 0xFF;
                int v = yuv[uvIndex + 1] & 0xFF;

                // Adjust for signed values
                u -= 128;
                v -= 128;

                // Convert YUV to RGB
                int r = y + (int) (1.402f * v);
                int g = y - (int) (0.344f * u + 0.714f * v);
                int b = y + (int) (1.772f * u);

                // Clamp values to [0, 255]
                r = Math.max(0, Math.min(255, r));
                g = Math.max(0, Math.min(255, g));
                b = Math.max(0, Math.min(255, b));

                // Pack into an integer (ARGB format)
                rgbOutput[j * width + i] = (0xFF << 24) | (r << 16) | (g << 8) | b;
            }
        }
    }

    public static void convertYuvToRgb(Image image, Bitmap bitmapBuffer) {
        if (image == null || bitmapBuffer == null) {
            throw new IllegalArgumentException("Image or BitmapBuffer is null!");
        }

        // Get image planes
        Image.Plane[] planes = image.getPlanes();
        ByteBuffer yBuffer = planes[0].getBuffer(); // Y
        ByteBuffer uBuffer = planes[1].getBuffer(); // U
        ByteBuffer vBuffer = planes[2].getBuffer(); // V

        int width = image.getWidth();
        int height = image.getHeight();

        // Row strides for each plane
        int yRowStride = planes[0].getRowStride();
        int uvRowStride = planes[1].getRowStride();
        int uvPixelStride = planes[1].getPixelStride();

        int[] rgbOutput = new int[width * height];

        for (int j = 0; j < height; j++) {
            for (int i = 0; i < width; i++) {
                // Y plane
                int yIndex = j * yRowStride + i;
                int y = yBuffer.get(yIndex) & 0xFF;

                // UV plane (interleaved)
                int uvIndex = (j / 2) * uvRowStride + (i / 2) * uvPixelStride;
                int u = uBuffer.get(uvIndex) & 0xFF;
                int v = vBuffer.get(uvIndex) & 0xFF;

                // Adjust for signed U and V values
                u -= 128;
                v -= 128;

                // Convert YUV to RGB
                int r = y + (int) (1.402f * v);
                int g = y - (int) (0.344f * u + 0.714f * v);
                int b = y + (int) (1.772f * u);

                // Clamp RGB values to [0, 255]
                r = Math.max(0, Math.min(255, r));
                g = Math.max(0, Math.min(255, g));
                b = Math.max(0, Math.min(255, b));

                // Write to RGB output array
                rgbOutput[j * width + i] = (0xFF << 24) | (r << 16) | (g << 8) | b;
            }
        }

        // Write RGB data into Bitmap
        bitmapBuffer.setPixels(rgbOutput, 0, width, 0, 0, width, height);
    }
}
